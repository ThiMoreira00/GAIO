<?php

/**
 * @file AlunoController.php
 * @description Controlador responsável pelo gerenciamento das requisições que envolvem os alunos
 * @author Thiago Moreira
 * @copyright Copyright (c) 2025
 */

// Declaração de namespace
namespace App\Controllers;

// Importação de classes
use App\Core\Controller;
use App\Core\Request;
use App\Models\Aluno;
use App\Models\AlunoDocumento;
use App\Models\AlunoDocumentoTipo;
use App\Models\AlunoIngressoTipo;
use App\Models\AlunoMatricula;
use App\Models\AlunoResponsavel;
use App\Models\Curso;
use App\Models\Enumerations\AlunoEscolaNivel;
use App\Models\Enumerations\CursoStatus;
use App\Models\Usuario;
use App\Models\Enumerations\AlunoResponsavelTipo;
use App\Models\Enumerations\Turno;
use App\Models\Enumerations\AlunoMatriculaStatus;
use App\Models\Enumerations\EnsinoModalidade;
use App\Models\Enumerations\UsuarioEstadoCivil;
use App\Models\Enumerations\UsuarioSexo;
use App\Models\Enumerations\UsuarioCorRaca;
use App\Models\Enumerations\UF;
use App\Models\Enumerations\UsuarioNacionalidade;
use App\Models\Enumerations\UsuarioNecessidadeEspecifica;
use App\Models\UsuarioContato;
use App\Models\UsuarioLogin;
use App\Services\ViaCepService;
use Illuminate\Support\Facades\DB;
use DateTime;
use Exception;
use PhpOffice\PhpSpreadsheet\IOFactory;

/**
 * Classe AlunoController
 *
 * Gerencia as requisições que envolvem os alunos
 *
 * @package App\Controllers
 * @extends Controller
 */
class AlunoController extends Controller
{

    // --- MÉTODOS DE VISUALIZAÇÃO ---

    /**
     * Renderiza a página inicial de alunos
     *
     * @return void
     */
    public function exibirIndex(): void
    {
        // Breadcrumbs = links de navegação
        $breadcrumbs = [
            ['label' => 'Administração', 'url' => '/admin'],
            ['label' => 'Alunos', 'url' => '/alunos']
        ];

        // Obtém todos os cursos ativos
        $cursosAtivos = Curso::obterTodos()->where('status', 'ATIVO');

        // Obtém todas as necessidades específicas
        $necessidadesEspecificas = UsuarioNecessidadeEspecifica::cases();

        // Obtém todos os estados civis
        $estadosCivis = UsuarioEstadoCivil::cases();

        // Obtém todos os sexos
        $sexos = UsuarioSexo::cases();

        // Obtém todas as cores/raças
        $coresRacas = UsuarioCorRaca::cases();

        // Obtém todos os UFs
        $ufs = UF::cases();

        // Obtém todos os níveis de ensino
        $escolaNiveis = AlunoEscolaNivel::cases();

        // Obtém todas as nacionalidades
        $nacionalidades = UsuarioNacionalidade::cases();

        // Obtém todos os tipos de responsável
        $tiposResponsavel = AlunoResponsavelTipo::cases();

        // Obtém todos os tipos de documentos
        $tiposDocumentos = AlunoDocumentoTipo::obterTodos();

        // Obtém todos os turnos de ingresso
        $turnosIngresso = Turno::cases();

        // Obtém todos os tipos de ingresso
        $tiposIngresso = AlunoIngressoTipo::obterTodos()->where('status', 'ATIVO');

        // Renderiza a página de alunos
        $this->renderizar('alunos/index', [
            'titulo' => 'Alunos',
            'breadcrumbs' => $breadcrumbs,
            'cursos' => $cursosAtivos,

            // Para o modal de adicionar/editar aluno
            'necessidadesEspecificas' => $necessidadesEspecificas,
            'estadosCivis' => $estadosCivis,
            'sexos' => $sexos,
            'coresRaca' => $coresRacas,
            'ufs' => $ufs,
            'escolaNiveis' => $escolaNiveis,
            'nacionalidades' => $nacionalidades,
            'tiposResponsavel' => $tiposResponsavel,
            'tiposDocumentos' => $tiposDocumentos,
            'turnosIngresso' => $turnosIngresso,
            'tiposIngresso' => $tiposIngresso
        ]);
    }


    // --- MÉTODOS DE REQUISIÇÕES ---

    /**
     * Função para filtrar os alunos com base nos parâmetros enviados
     * 
     * @param Request $request
     * @return void
     */
    public function filtrarAlunos(Request $request): void
    {
        try {

            // TODO: Adicionar token CSRF

            // Parâmetros de filtro
            $status = $request->get('status') ?? null;
            $pagina = $request->get('pagina') ?? null;
            $busca = $request->get('busca') ?? null;
            $situacaoMatricula = $request->get('situacao_matricula') ?? null;
            $limite = $request->get('limite') ?? 15;

            $query = Aluno::query()->with('usuario');

            if ($status) {
                $query->where('status', $status);
            }

            // Filtrar por busca (nome, email ou CPF) - buscar no relacionamento usuario
            if ($busca) {
                $query->whereHas('usuario', function ($subquery) use ($busca) {
                    $subquery->where('nome_civil', 'LIKE', "%$busca%")
                        ->orWhere('nome_social', 'LIKE', "%$busca%")
                        ->orWhere('email_pessoal', 'LIKE', "%$busca%")
                        ->orWhere('email_institucional', 'LIKE', "%$busca%")
                        ->orWhere('cpf', 'LIKE', "%$busca%");
                })->orWhereHas('matriculas', function ($subquery) use ($busca) {
                    $subquery->where('matricula', 'LIKE', "%$busca%");
                });
            }

            if ($situacaoMatricula) {
                $query->whereHas('matricula', function ($subquery) use ($situacaoMatricula) {
                    $subquery->where('status', strtoupper($situacaoMatricula));
                });
            }

            $paginator = $query->paginate($limite, ['*'], 'page', $pagina);

            // Ordenar: arquivados primeiro, depois por nome
            $items = $paginator->getCollection()->values();

            // Para cada entrada, unificar os dados do aluno e do usuário (removendo o objeto usuário)
            $items = $items->map(function ($aluno) {

                $dadosUsuario = $aluno->usuario->obterDados();
                $dadosUsuario['foto_perfil'] = $aluno->obterFotoPerfil();

                $dadosAluno = $aluno->obterDados();

                // Busca a última matrícula com base na data de registro
                $ultimaMatricula = $aluno->obterUltimaMatricula();

                // Converte para array de forma segura, com enums formatados
                if ($ultimaMatricula) {

                    $dadosMatricula = $ultimaMatricula->obterDados();

                    // Remove campos desnecessários da matrícula (para a visualização do aluno)
                    unset($dadosMatricula['id']);
                    unset($dadosMatricula['aluno_id']);
                    unset($dadosMatricula['matriz_id']);
                    unset($dadosMatricula['data_matricula']);
                    unset($dadosMatricula['data_registro']);
                    unset($dadosMatricula['ingresso_id']);
                    unset($dadosMatricula['ingresso_classificacao']);
                    unset($dadosMatricula['ingresso_pontos']);

                    $dadosAluno['matricula'] = $dadosMatricula;


                } else {
                    $dadosAluno['matricula'] = null;
                }

                unset($dadosAluno['usuario']);
                unset($dadosAluno['usuario_id']);

                return array_merge($dadosAluno, $dadosUsuario);
            });

            // DEBUG
            // Filtrar para que apenas alunos com matrícula sejam retornados 
            $items = $items->filter(function ($aluno) {
                return isset($aluno['matricula']);
            })->values();

            $this->responderJSON([
                'status' => 'sucesso',
                'data' => $items,
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'total' => $paginator->total()
            ]);

        } catch (Exception $e) {

            $this->responderJSON([
                'status' => 'erro',
                'mensagem' => $e->getMessage()
            ], $e->getCode() ?: 500);
        }
    }


    /**
     * Função para adicionar um novo aluno
     * 
     * @param Request $request
     * @return void
     */
    public function adicionarAluno(Request $request): void
    {
        try {

            // TODO: Validar token CSRF
            $this->validarTokenCSRF($request);

            // Extrai os dados do formulário
            $dados = [
                'nome_civil' => trim($request->post('nome_civil', '')),
                'nome_social' => trim($request->post('nome_social', '')),
                'rg' => trim($request->post('rg', '')),
                'cpf' => trim($request->post('cpf', '')),
                'email_pessoal' => trim($request->post('email_pessoal', '')),
                'email_institucional' => trim($request->post('email_institucional', '')),
                'sexo' => $request->post('sexo', ''),
                'cor_raca' => $request->post('cor_raca', ''),
                'data_nascimento' => $request->post('data_nascimento', ''),
                'estado_civil' => $request->post('estado_civil', ''),
                'nacionalidade' => $request->post('nacionalidade', ''),
                'naturalidade' => trim($request->post('naturalidade', '')),
                'necessidades_especificas' => $request->post('necessidades_especificas', []),
                'cep' => preg_replace('/\D/', '', $request->post('cep', '')),
                'endereco' => trim($request->post('endereco', '')),
                'numero' => trim($request->post('numero', '')),
                'complemento' => trim($request->post('complemento', '')),
                'bairro' => trim($request->post('bairro', '')),
                'cidade' => trim($request->post('cidade', '')),
                'uf' => $request->post('uf', ''),
                'telefone_fixo' => preg_replace('/\D/', '', $request->post('telefone_fixo', '')),
                'telefone_celular' => preg_replace('/\D/', '', $request->post('telefone_celular', '')),
                'nome_instituicao' => trim($request->post('nome_instituicao', '')),
                'cidade_instituicao' => trim($request->post('cidade_instituicao', '')),
                'uf_instituicao' => $request->post('uf_instituicao', ''),
                'ano_conclusao' => trim($request->post('ano_conclusao', '')),
                'nivel_ensino' => $request->post('nivel_ensino', ''),
                'responsaveis' => $request->post('responsaveis', []),
                'documentos' => $request->post('documentos', []),
                'matricula' => $request->post('matricula', [])
            ];


            $erros = [];

            // ======================================================
            // 1. INFORMAÇÕES BÁSICAS
            // ======================================================

            // Nome civil
            if (empty($dados['nome_civil'])) {
                $erros[] = 'O nome civil é obrigatório.';
            } elseif (strlen($dados['nome_civil']) < 3) {
                $erros[] = 'O nome civil deve ter no mínimo 3 caracteres.';
            } elseif (strlen($dados['nome_civil']) > 100) {
                $erros[] = 'O nome civil não pode exceder 100 caracteres.';
            }

            // Nome social (opcional)
            if (!empty($dados['nome_social']) && strlen($dados['nome_social']) > 100) {
                $erros[] = 'O nome social não pode exceder 100 caracteres.';
            }

            // RG
            if (empty($dados['rg'])) {
                $erros[] = 'O RG é obrigatório.';
            } elseif (!Usuario::validarRG($dados['rg'])) {
                $erros[] = 'O RG fornecido é inválido.';
            } elseif (Usuario::buscarPorRG($dados['rg'])) {
                $erros[] = 'O RG informado já está cadastrado no sistema.';
            }

            // CPF
            if (empty($dados['cpf'])) {
                $erros[] = 'O CPF é obrigatório.';
            } elseif (!Usuario::validarCPF($dados['cpf'])) {
                $erros[] = 'O CPF fornecido é inválido.';
            } elseif (Usuario::buscarPorCPF($dados['cpf'])) {
                $erros[] = 'O CPF informado já está cadastrado no sistema.';
            }

            // E-mail pessoal
            if (empty($dados['email_pessoal'])) {
                $erros[] = 'O e-mail pessoal é obrigatório.';
            } elseif (!filter_var($dados['email_pessoal'], FILTER_VALIDATE_EMAIL)) {
                $erros[] = 'O e-mail pessoal fornecido é inválido.';
            } elseif (Usuario::buscarPorEmailPessoal($dados['email_pessoal'])) {
                $erros[] = 'O e-mail pessoal informado já está cadastrado no sistema.';
            }

            // E-mail institucional (opcional)
            if (!empty($dados['email_institucional'])) {
                if (!filter_var($dados['email_institucional'], FILTER_VALIDATE_EMAIL)) {
                    $erros[] = 'O e-mail institucional fornecido é inválido.';
                } elseif (Usuario::buscarPorEmailInstitucional($dados['email_institucional'])) {
                    $erros[] = 'O e-mail institucional informado já está cadastrado no sistema.';
                } else {
                    $dominio = substr(strrchr($dados['email_institucional'], '@'), 1);
                    if (!preg_match('/\.(edu|gov|org|br)$/', $dominio)) {
                        $erros[] = 'O e-mail institucional deve pertencer a um domínio educacional (.edu, .gov, .org ou .br).';
                    }
                }
            }

            // ===========================
            // 2. DADOS PESSOAIS
            // ===========================

            // Sexo
            if (empty($dados['sexo'])) {
                $erros[] = 'O sexo é obrigatório.';
            } elseif (!UsuarioSexo::fromName($dados['sexo'])) {
                $erros[] = 'O sexo selecionado é inválido.';
            }

            // Cor/Raça
            if (empty($dados['cor_raca'])) {
                $erros[] = 'A cor/raça é obrigatória.';
            } elseif (!UsuarioCorRaca::fromName($dados['cor_raca'])) {
                $erros[] = 'A cor/raça selecionada é inválida.';
            }

            // Data de nascimento
            if (empty($dados['data_nascimento'])) {
                $erros[] = 'A data de nascimento é obrigatória.';
            } else {
                $dataNascimento = DateTime::createFromFormat('Y-m-d', $dados['data_nascimento']);
                if (!$dataNascimento || $dataNascimento->format('Y-m-d') !== $dados['data_nascimento']) {
                    $erros[] = 'A data de nascimento fornecida é inválida.';
                } else {
                    $idade = $dataNascimento->diff(new DateTime())->y;
                    if ($idade < 14) {
                        $erros[] = 'O aluno deve ter no mínimo 14 anos.';
                    } elseif ($idade > 120) {
                        $erros[] = 'A data de nascimento fornecida é inválida.';
                    }
                }
            }

            // Estado civil
            if (empty($dados['estado_civil'])) {
                $erros[] = 'O estado civil é obrigatório.';
            } elseif (!UsuarioEstadoCivil::fromName($dados['estado_civil'])) {
                $erros[] = 'O estado civil selecionado é inválido.';
            }

            // Nacionalidade
            if (empty($dados['nacionalidade'])) {
                $erros[] = 'A nacionalidade é obrigatória.';
            } elseif (!UsuarioNacionalidade::fromName($dados['nacionalidade'])) {
                $erros[] = 'A nacionalidade selecionada é inválida.';
            }

            // Naturalidade
            if (empty($dados['naturalidade'])) {
                $erros[] = 'A naturalidade é obrigatória.';
            } elseif (strlen($dados['naturalidade']) > 100) {
                $erros[] = 'A naturalidade não pode exceder 100 caracteres.';
            }

            // Necessidades específicas (opcional)
            if (!empty($dados['necessidades_especificas'])) {
                if (!is_array($dados['necessidades_especificas'])) {
                    $erros[] = 'As necessidades específicas devem ser fornecidas em formato de lista.';
                } else {
                    foreach ($dados['necessidades_especificas'] as $necessidade) {
                        if (!UsuarioNecessidadeEspecifica::fromName($necessidade)) {
                            $erros[] = 'Uma ou mais necessidades específicas selecionadas são inválidas.';
                            break;
                        }
                    }
                }
            }


            // ===========================
            // 3. DADOS DE CONTATO
            // ===========================

            // CEP
            if (empty($dados['cep'])) {
                $erros[] = 'O CEP é obrigatório.';
            } elseif (strlen($dados['cep']) !== 8) {
                $erros[] = 'O CEP deve conter 8 dígitos.';
            } else {
                $viaCepService = new ViaCepService();
                $dadosEndereco = $viaCepService->buscarEndereco($dados['cep']);

                if (!$dadosEndereco) {
                    $erros[] = 'O CEP informado não foi encontrado.';
                } else {
                    // Valida correspondência com dados do ViaCEP
                    if (!empty($dadosEndereco['logradouro']) && strcasecmp($dadosEndereco['logradouro'], $dados['endereco']) !== 0) {
                        $erros[] = 'O logradouro não corresponde ao CEP informado.';
                    }
                    if (!empty($dadosEndereco['bairro']) && strcasecmp($dadosEndereco['bairro'], $dados['bairro']) !== 0) {
                        $erros[] = 'O bairro não corresponde ao CEP informado.';
                    }
                    if (!empty($dadosEndereco['localidade']) && strcasecmp($dadosEndereco['localidade'], $dados['cidade']) !== 0) {
                        $erros[] = 'A cidade não corresponde ao CEP informado.';
                    }
                    if (!empty($dadosEndereco['uf']) && strcasecmp($dadosEndereco['uf'], $dados['uf']) !== 0) {
                        $erros[] = 'O estado não corresponde ao CEP informado.';
                    }
                }
            }

            // Endereço
            if (empty($dados['endereco'])) {
                $erros[] = 'O endereço é obrigatório.';
            } elseif (strlen($dados['endereco']) > 200) {
                $erros[] = 'O endereço não pode exceder 200 caracteres.';
            }

            // Número
            if (empty($dados['numero'])) {
                $erros[] = 'O número do endereço é obrigatório.';
            } elseif (strlen($dados['numero']) > 20) {
                $erros[] = 'O número do endereço não pode exceder 20 caracteres.';
            }

            // Complemento (opcional)
            if (!empty($dados['complemento']) && strlen($dados['complemento']) > 100) {
                $erros[] = 'O complemento não pode exceder 100 caracteres.';
            }

            // Bairro
            if (empty($dados['bairro'])) {
                $erros[] = 'O bairro é obrigatório.';
            } elseif (strlen($dados['bairro']) > 100) {
                $erros[] = 'O bairro não pode exceder 100 caracteres.';
            }

            // Cidade
            if (empty($dados['cidade'])) {
                $erros[] = 'A cidade é obrigatória.';
            } elseif (strlen($dados['cidade']) > 100) {
                $erros[] = 'A cidade não pode exceder 100 caracteres.';
            }

            // UF
            if (empty($dados['uf'])) {
                $erros[] = 'O estado (UF) é obrigatório.';
            } elseif (!UF::fromName($dados['uf'])) {
                $erros[] = 'O estado (UF) selecionado é inválido.';
            }

            // Telefone fixo (opcional)
            if (!empty($dados['telefone_fixo']) && !UsuarioContato::validarTelefoneFixo($dados['telefone_fixo'])) {
                $erros[] = 'O telefone fixo fornecido é inválido.';
            }

            // Telefone celular
            if (empty($dados['telefone_celular'])) {
                $erros[] = 'O telefone celular é obrigatório.';
            } elseif (!UsuarioContato::validarTelefoneCelular($dados['telefone_celular'])) {
                $erros[] = 'O telefone celular fornecido é inválido.';
            }

            // ===========================
            // 4. DADOS ESCOLARES
            // ===========================

            // Nome da instituição
            if (empty($dados['nome_instituicao'])) {
                $erros[] = 'O nome da instituição de ensino é obrigatório.';
            } elseif (strlen($dados['nome_instituicao']) > 200) {
                $erros[] = 'O nome da instituição não pode exceder 200 caracteres.';
            }

            // Cidade da instituição
            if (empty($dados['cidade_instituicao'])) {
                $erros[] = 'A cidade da instituição é obrigatória.';
            } elseif (strlen($dados['cidade_instituicao']) > 100) {
                $erros[] = 'A cidade da instituição não pode exceder 100 caracteres.';
            }

            // UF da instituição
            if (empty($dados['uf_instituicao'])) {
                $erros[] = 'O estado da instituição é obrigatório.';
            } elseif (!UF::fromName($dados['uf_instituicao'])) {
                $erros[] = 'O estado da instituição selecionado é inválido.';
            }

            // Ano de conclusão
            if (empty($dados['ano_conclusao'])) {
                $erros[] = 'O ano de conclusão é obrigatório.';
            } elseif (!preg_match('/^\d{4}$/', $dados['ano_conclusao'])) {
                $erros[] = 'O ano de conclusão deve ter 4 dígitos.';
            } elseif ((int) $dados['ano_conclusao'] < 1950 || (int) $dados['ano_conclusao'] > (int) date('Y') + 1) {
                $erros[] = 'O ano de conclusão informado é inválido.';
            }

            // Nível de ensino
            if (empty($dados['nivel_ensino'])) {
                $erros[] = 'O nível de ensino é obrigatório.';
            } elseif (!EnsinoModalidade::fromName($dados['nivel_ensino'])) {
                $erros[] = 'O nível de ensino selecionado é inválido.';
            }

            // ===========================
            // 5. FILIAÇÃO (RESPONSÁVEIS)
            // ===========================

            if (empty($dados['responsaveis']) || !is_array($dados['responsaveis'])) {
                $erros[] = 'Pelo menos um responsável deve ser informado.';
            } else {
                $responsaveisValidos = 0;
                foreach ($dados['responsaveis'] as $index => $responsavel) {
                    $numResponsavel = $index + 1;

                    // Se há nome preenchido, valida os campos
                    if (!empty($responsavel['nome'])) {
                        $responsaveisValidos++;

                        if (strlen($responsavel['nome']) < 3) {
                            $erros[] = "O nome do responsável #{$numResponsavel} deve ter no mínimo 3 caracteres.";
                        } elseif (strlen($responsavel['nome']) > 100) {
                            $erros[] = "O nome do responsável #{$numResponsavel} não pode exceder 100 caracteres.";
                        }

                        if (empty($responsavel['tipo'])) {
                            $erros[] = "O tipo do responsável #{$numResponsavel} é obrigatório.";
                        } elseif (!AlunoResponsavelTipo::fromName($responsavel['tipo'])) {
                            $erros[] = "O tipo do responsável #{$numResponsavel} é inválido.";
                        }
                    }
                }

                if ($responsaveisValidos === 0) {
                    $erros[] = 'Pelo menos um responsável deve ser informado.';
                }
            }

            // ===========================
            // 6. DOCUMENTOS
            // ===========================

            // RG (obrigatório)
            if (empty($dados['documentos']['rg'])) {
                $erros[] = 'Os dados do documento RG são obrigatórios.';
            } else {
                $documentoRegistroGeral = $dados['documentos']['rg'];

                if (empty($documentoRegistroGeral['numero'])) {
                    $erros[] = 'O número do RG é obrigatório.';
                } elseif (strlen($documentoRegistroGeral['numero']) > 20) {
                    $erros[] = 'O número do RG não pode exceder 20 caracteres.';
                }

                if (empty($documentoRegistroGeral['orgao_emissor'])) {
                    $erros[] = 'O órgão emissor do RG é obrigatório.';
                } elseif (strlen($documentoRegistroGeral['orgao_emissor']) > 20) {
                    $erros[] = 'O órgão emissor do RG não pode exceder 20 caracteres.';
                }

                if (empty($documentoRegistroGeral['data_emissao'])) {
                    $erros[] = 'A data de emissão do RG é obrigatória.';
                } else {
                    $dataEmissaoRegistroGeral = DateTime::createFromFormat('Y-m-d', $documentoRegistroGeral['data_emissao']);
                    if (!$dataEmissaoRegistroGeral || $dataEmissaoRegistroGeral->format('Y-m-d') !== $documentoRegistroGeral['data_emissao']) {
                        $erros[] = 'A data de emissão do RG é inválida.';
                    } elseif ($dataEmissaoRegistroGeral > new DateTime()) {
                        $erros[] = 'A data de emissão do RG não pode ser futura.';
                    }
                }

                if (empty($documentoRegistroGeral['uf_emissor'])) {
                    $erros[] = 'A UF emissora do RG é obrigatória.';
                } elseif (!UF::fromName($documentoRegistroGeral['uf_emissor'])) {
                    $erros[] = 'A UF emissora do RG é inválida.';
                }
            }

            // CPF (obrigatório)
            if (empty($dados['documentos']['cpf'])) {
                $erros[] = 'Os dados do documento CPF são obrigatórios.';
            } else {
                $documentoCPF = $dados['documentos']['cpf'];

                if (empty($documentoCPF['cpf'])) {
                    $erros[] = 'O número do CPF no documento é obrigatório.';
                } elseif (!Usuario::validarCPF($documentoCPF['cpf'])) {
                    $erros[] = 'O número do CPF no documento é inválido.';
                }

                if (!isset($documentoCPF['cpf_proprio'])) {
                    $erros[] = 'Informe se o CPF é do próprio aluno.';
                } elseif (!in_array($documentoCPF['cpf_proprio'], ['0', '1'], true)) {
                    $erros[] = 'O campo "CPF próprio" é inválido.';
                }
            }

            // Certidão de Nascimento (obrigatório)
            if (!empty(array_filter($dados['documentos']['nascimento'])) && is_array($dados['documentos']['nascimento'])) {
                $documentoNascimento = $dados['documentos']['nascimento'];

                if (empty($documentoNascimento['numero'])) {
                    $erros[] = 'O número da Certidão de Nascimento é obrigatório.';
                } elseif (strlen($documentoNascimento['numero']) > 50) {
                    $erros[] = 'O número da Certidão de Nascimento não pode exceder 50 caracteres.';
                }

                // UF (opcional)
                if (!empty($documentoNascimento['uf']) && !UF::fromName($documentoNascimento['uf'])) {
                    $erros[] = 'A UF da Certidão de Nascimento é inválida.';
                }

                // Livro, folha, termo, cartório (opcionais com limite de caracteres)
                if (!empty($documentoNascimento['livro']) && strlen($documentoNascimento['livro']) > 20) {
                    $erros[] = 'O livro da Certidão de Nascimento não pode exceder 20 caracteres.';
                }
                if (!empty($documentoNascimento['folha']) && strlen($documentoNascimento['folha']) > 20) {
                    $erros[] = 'A folha da Certidão de Nascimento não pode exceder 20 caracteres.';
                }
                if (!empty($documentoNascimento['termo']) && strlen($documentoNascimento['termo']) > 20) {
                    $erros[] = 'O termo da Certidão de Nascimento não pode exceder 20 caracteres.';
                }
                if (!empty($documentoNascimento['cartorio']) && strlen($documentoNascimento['cartorio']) > 100) {
                    $erros[] = 'O cartório da Certidão de Nascimento não pode exceder 100 caracteres.';
                }

                // Data de emissão (opcional)
                if (!empty($documentoNascimento['data_emissao'])) {
                    $dataEmissaoNascimento = DateTime::createFromFormat('Y-m-d', $documentoNascimento['data_emissao']);
                    if (!$dataEmissaoNascimento || $dataEmissaoNascimento->format('Y-m-d') !== $documentoNascimento['data_emissao']) {
                        $erros[] = 'A data de emissão da Certidão de Nascimento é inválida.';
                    } elseif ($dataEmissaoNascimento > new DateTime()) {
                        $erros[] = 'A data de emissão da Certidão de Nascimento não pode ser futura.';
                    }
                }
            }

            // Certidão de Casamento (opcional)
            if (!empty($dados['documentos']['casamento']) && is_array($dados['documentos']['casamento'])) {
                $documentoCasamento = $dados['documentos']['casamento'];

                // Se algum campo estiver preenchido, valida
                $algumCampoPreenchido = !empty($documentoCasamento['numero']) || !empty($documentoCasamento['uf']) ||
                    !empty($documentoCasamento['livro']) || !empty($documentoCasamento['folha']) ||
                    !empty($documentoCasamento['termo']) || !empty($documentoCasamento['cartorio']) ||
                    !empty($documentoCasamento['data_emissao']);

                if ($algumCampoPreenchido) {
                    // Número (opcional com limite)
                    if (!empty($documentoCasamento['numero']) && strlen($documentoCasamento['numero']) > 50) {
                        $erros[] = 'O número da Certidão de Casamento não pode exceder 50 caracteres.';
                    }

                    // UF (opcional)
                    if (!empty($documentoCasamento['uf']) && !UF::fromName($documentoCasamento['uf'])) {
                        $erros[] = 'A UF da Certidão de Casamento é inválida.';
                    }

                    // Livro, folha, termo, cartório (opcionais com limite)
                    if (!empty($documentoCasamento['livro']) && strlen($documentoCasamento['livro']) > 20) {
                        $erros[] = 'O livro da Certidão de Casamento não pode exceder 20 caracteres.';
                    }
                    if (!empty($documentoCasamento['folha']) && strlen($documentoCasamento['folha']) > 20) {
                        $erros[] = 'A folha da Certidão de Casamento não pode exceder 20 caracteres.';
                    }
                    if (!empty($documentoCasamento['termo']) && strlen($documentoCasamento['termo']) > 20) {
                        $erros[] = 'O termo da Certidão de Casamento não pode exceder 20 caracteres.';
                    }
                    if (!empty($documentoCasamento['cartorio']) && strlen($documentoCasamento['cartorio']) > 100) {
                        $erros[] = 'O cartório da Certidão de Casamento não pode exceder 100 caracteres.';
                    }

                    // Data de emissão (opcional)
                    if (!empty($documentoCasamento['data_emissao'])) {
                        $dataEmissaoCasamento = DateTime::createFromFormat('Y-m-d', $documentoCasamento['data_emissao']);
                        if (!$dataEmissaoCasamento || $dataEmissaoCasamento->format('Y-m-d') !== $documentoCasamento['data_emissao']) {
                            $erros[] = 'A data de emissão da Certidão de Casamento é inválida.';
                        } elseif ($dataEmissaoCasamento > new DateTime()) {
                            $erros[] = 'A data de emissão da Certidão de Casamento não pode ser futura.';
                        }
                    }
                }
            }

            // Carteira de Trabalho (opcional)
            if (!empty($dados['documentos']['carteira_trabalho']) && is_array($dados['documentos']['carteira_trabalho'])) {
                $documentoCarteiraTrabalho = $dados['documentos']['carteira_trabalho'];

                if (!empty($documentoCarteiraTrabalho['numero'])) {
                    if (strlen($documentoCarteiraTrabalho['numero']) > 20) {
                        $erros[] = 'O número da Carteira de Trabalho não pode exceder 20 caracteres.';
                    }

                    // Série (opcional)
                    if (!empty($documentoCarteiraTrabalho['serie']) && strlen($documentoCarteiraTrabalho['serie']) > 20) {
                        $erros[] = 'A série da Carteira de Trabalho não pode exceder 20 caracteres.';
                    }
                }
            }

            // Título de Eleitor (opcional)
            if (!empty($dados['documentos']['titulo_eleitor']) && is_array($dados['documentos']['titulo_eleitor'])) {
                $documentoTituloEleitor = $dados['documentos']['titulo_eleitor'];

                // Número (opcional com limite)
                if (!empty($documentoTituloEleitor['numero']) && strlen($documentoTituloEleitor['numero']) > 20) {
                    $erros[] = 'O número do Título de Eleitor não pode exceder 20 caracteres.';
                }

                // Data de emissão (opcional)
                if (!empty($documentoTituloEleitor['data_emissao'])) {
                    $dataEmissaoTituloEleitor = DateTime::createFromFormat('Y-m-d', $documentoTituloEleitor['data_emissao']);
                    if (!$dataEmissaoTituloEleitor || $dataEmissaoTituloEleitor->format('Y-m-d') !== $documentoTituloEleitor['data_emissao']) {
                        $erros[] = 'A data de emissão do Título de Eleitor é inválida.';
                    } elseif ($dataEmissaoTituloEleitor > new DateTime()) {
                        $erros[] = 'A data de emissão do Título de Eleitor não pode ser futura.';
                    }
                }

                // Zona, seção, município (opcionais com limite)
                if (!empty($documentoTituloEleitor['zona']) && strlen($documentoTituloEleitor['zona']) > 10) {
                    $erros[] = 'A zona eleitoral não pode exceder 10 caracteres.';
                }
                if (!empty($documentoTituloEleitor['secao']) && strlen($documentoTituloEleitor['secao']) > 10) {
                    $erros[] = 'A seção eleitoral não pode exceder 10 caracteres.';
                }
                if (!empty($documentoTituloEleitor['municipio']) && strlen($documentoTituloEleitor['municipio']) > 100) {
                    $erros[] = 'O município do Título de Eleitor não pode exceder 100 caracteres.';
                }
            }

            // Certificado de Alistamento Militar (opcional)
            if (!empty($dados['documentos']['alistamento']) && is_array($dados['documentos']['alistamento'])) {
                $documentoAlistamentoMilitar = $dados['documentos']['alistamento'];

                // Número (opcional)
                if (!empty($documentoAlistamentoMilitar['numero']) && strlen($documentoAlistamentoMilitar['numero']) > 20) {
                    $erros[] = 'O número do Certificado de Alistamento não pode exceder 20 caracteres.';
                }

                // Série (opcional)
                if (!empty($documentoAlistamentoMilitar['serie']) && strlen($documentoAlistamentoMilitar['serie']) > 20) {
                    $erros[] = 'A série do Certificado de Alistamento não pode exceder 20 caracteres.';
                }

                // Data (opcional)
                if (!empty($documentoAlistamentoMilitar['data'])) {
                    $dataAlistamentoMilitar = DateTime::createFromFormat('Y-m-d', $documentoAlistamentoMilitar['data']);
                    if (!$dataAlistamentoMilitar || $dataAlistamentoMilitar->format('Y-m-d') !== $documentoAlistamentoMilitar['data']) {
                        $erros[] = 'A data do Certificado de Alistamento é inválida.';
                    } elseif ($dataAlistamentoMilitar > new DateTime()) {
                        $erros[] = 'A data do Certificado de Alistamento não pode ser futura.';
                    }
                }
            }

            // Certificado de Reservista (opcional)
            if (!empty($dados['documentos']['reservista']) && is_array($dados['documentos']['reservista'])) {
                $documentoReservista = $dados['documentos']['reservista'];

                // RM (opcional)
                if (!empty($documentoReservista['rm']) && strlen($documentoReservista['rm']) > 20) {
                    $erros[] = 'O RM do Certificado de Reservista não pode exceder 20 caracteres.';
                }

                // CAT (opcional)
                if (!empty($documentoReservista['cat']) && strlen($documentoReservista['cat']) > 20) {
                    $erros[] = 'O CAT do Certificado de Reservista não pode exceder 20 caracteres.';
                }

                // CSM (opcional)
                if (!empty($documentoReservista['csm']) && strlen($documentoReservista['csm']) > 20) {
                    $erros[] = 'O CSM do Certificado de Reservista não pode exceder 20 caracteres.';
                }

                // Data (opcional)
                if (!empty($documentoReservista['data'])) {
                    $dataReservista = DateTime::createFromFormat('Y-m-d', $documentoReservista['data']);
                    if (!$dataReservista || $dataReservista->format('Y-m-d') !== $documentoReservista['data']) {
                        $erros[] = 'A data do Certificado de Reservista é inválida.';
                    } elseif ($dataReservista > new DateTime()) {
                        $erros[] = 'A data do Certificado de Reservista não pode ser futura.';
                    }
                }
            }

            // ===========================
            // 7. MATRÍCULA
            // ===========================

            if (empty($dados['matricula']) || !is_array($dados['matricula'])) {
                $erros[] = 'As informações de matrícula são obrigatórias.';
            } else {
                $matricula = $dados['matricula'];

                // Curso
                if (empty($matricula['curso_id'])) {
                    $erros[] = 'O curso da matrícula é obrigatório.';
                } else {
                    $curso = Curso::buscarPorId($matricula['curso_id']);
                    if (!$curso) {
                        $erros[] = 'O curso selecionado não foi encontrado.';
                    } elseif ($curso->obterStatus() !== CursoStatus::ATIVO) {
                        $erros[] = 'O curso selecionado não está ativo.';
                    } elseif (!$curso->obterMatrizVigente()) {
                        $erros[] = 'O curso selecionado não possui uma matriz curricular vigente.';
                    } else {
                        $matriz = $curso->obterMatrizVigente();
                    }
                }

                // Número da matrícula
                if (empty($matricula['numero'])) {
                    $erros[] = 'O número da matrícula é obrigatório.';
                } elseif (!is_numeric($matricula['numero'])) {
                    $erros[] = 'O número da matrícula deve conter apenas dígitos.';
                } elseif (strlen($matricula['numero']) > 20) {
                    $erros[] = 'O número da matrícula não pode exceder 20 caracteres.';
                } elseif (AlunoMatricula::buscarPorMatricula($matricula['numero'])) {
                    $erros[] = 'O número da matrícula informado já está cadastrado no sistema.';
                }

                // Data da matrícula
                if (empty($matricula['data'])) {
                    $erros[] = 'A data da matrícula é obrigatória.';
                } else {
                    $dataMatricula = DateTime::createFromFormat('Y-m-d', $matricula['data']);
                    if (!$dataMatricula || $dataMatricula->format('Y-m-d') !== $matricula['data']) {
                        $erros[] = 'A data da matrícula é inválida.';
                    } elseif ($dataMatricula > new DateTime()) {
                        $erros[] = 'A data da matrícula não pode ser futura.';
                    }
                }

                // Turno
                if (empty($matricula['turno'])) {
                    $erros[] = 'O turno da matrícula é obrigatório.';
                } elseif (!Turno::fromName($matricula['turno'])) {
                    $erros[] = 'O turno selecionado é inválido.';
                }

                // Forma de ingresso
                if (empty($matricula['forma_ingresso'])) {
                    $erros[] = 'A forma de ingresso é obrigatória.';
                } else {
                    $tipoIngresso = AlunoIngressoTipo::buscarPorId($matricula['forma_ingresso']);
                    if (!$tipoIngresso) {
                        $erros[] = 'A forma de ingresso selecionada não foi encontrada.';
                    } elseif (!$tipoIngresso->obterStatus()) {
                        $erros[] = 'A forma de ingresso selecionada não está ativa.';
                    }
                }

                // Pontuação (opcional)
                if (!empty($matricula['pontuacao'])) {
                    $pontuacao = str_replace(',', '.', $matricula['pontuacao']);
                    if (!is_numeric($pontuacao)) {
                        $erros[] = 'A pontuação deve ser um número válido.';
                    } elseif ((float) $pontuacao < 0 || (float) $pontuacao > 100) {
                        $erros[] = 'A pontuação deve estar entre 0 e 100.';
                    }
                }

                // Classificação (opcional)
                if (!empty($matricula['classificacao']) && !is_numeric($matricula['classificacao'])) {
                    $erros[] = 'A classificação deve ser um número válido.';
                }
            }

            // TODO: Iniciar a inserção de dados, mas antes, conferir


            // Verifica se possui algum erro
            if (!empty($erros)) {
                $this->responderJSON([
                    'status' => 'erro',
                    'mensagem' => 'Erros de validação encontrados.',
                    'erros' => $erros
                ], 400);
            }


            // ===========================
            // INSERÇÃO DOS DADOS
            // ===========================

            $usuario = Usuario::criar([
                'nome_civil' => $dados['nome_civil'],
                'nome_social' => $dados['nome_social'],
                'sexo' => $dados['sexo'],
                'cor_raca' => $dados['cor_raca'],
                'estado_civil' => $dados['estado_civil'],
                'cpf' => $dados['cpf'],
                'rg' => $dados['rg'],
                'nacionalidade' => $dados['nacionalidade'],
                'naturalidade' => $dados['naturalidade'],
                'email_pessoal' => $dados['email_pessoal'],
                'email_institucional' => $dados['email_institucional']
            ]);
            $usuario->salvar();

            $usuarioContato = UsuarioContato::criar([
                'usuario_id' => $usuario->obterId(),
                'cep' => $dados['cep'],
                'endereco' => $dados['endereco'],
                'numero' => $dados['numero'],
                'complemento' => $dados['complemento'],
                'bairro' => $dados['bairro'],
                'cidade' => $dados['cidade'],
                'uf' => $dados['uf'],
                'telefone_fixo' => $dados['telefone_fixo'],
                'telefone_celular' => $dados['telefone_celular']
            ]);
            $usuarioContato->salvar();

//            $usuarioLogin = UsuarioLogin::criar([
//                'usuario_id' => $usuario->obterId(),
//            ])

            $aluno = Aluno::criar([
                'usuario_id' => $usuario->obterId()
            ]);
            $aluno->salvar();

            $alunoMatricula = AlunoMatricula::criar([
                'aluno_id' => $aluno->obterId(),
                'matriz_id' => $matriz->obterId(),
                'matricula' => $dados['matricula']['numero'],
                'data_matricula' => $dados['matricula']['data'],

            ]);

            $this->removerTokenCSRF();

            $this->responderJSON([
                'status' => 'sucesso',
                'mensagem' => 'Aluno adicionado com sucesso!'
            ]);


        } catch (Exception $e) {
            $this->responderJSON([
                'status' => 'erro',
                'mensagem' => $e->getMessage()
            ], $e->getCode() ?: 500);
        }
    }

    /**
     * Função para importar alunos via SISU
     * 
     * @param Request $request
     * @return void
     */
    public function importarSISU(Request $request): void
    {
        try {
            // Inicia transação no banco de dados
            DB::beginTransaction();

            // Valida se o arquivo foi enviado
            if (!isset($_FILES['arquivo_sisu']) || $_FILES['arquivo_sisu']['error'] !== UPLOAD_ERR_OK) {
                throw new Exception('Nenhum arquivo foi enviado ou ocorreu um erro no upload.');
            }

            // Valida o tipo de importação
            $tipoImportacao = $request->post('tipo_importacao');
            if (!in_array($tipoImportacao, ['parcial', 'completa'])) {
                throw new Exception('Tipo de importação inválido.');
            }

            $arquivo = $_FILES['arquivo_sisu'];
            
            // Valida extensão do arquivo
            $extensoesValidas = ['xlsx', 'xls', 'csv'];
            $extensao = strtolower(pathinfo($arquivo['name'], PATHINFO_EXTENSION));
            
            if (!in_array($extensao, $extensoesValidas)) {
                throw new Exception('Formato de arquivo inválido. Use XLSX, XLS ou CSV.');
            }

            // Valida tamanho do arquivo (máx 10MB)
            $tamanhoMaxBytes = 10 * 1024 * 1024;
            if ($arquivo['size'] > $tamanhoMaxBytes) {
                throw new Exception('O arquivo é muito grande. Tamanho máximo: 10MB.');
            }

            // Carrega o arquivo com PhpSpreadsheet
            require_once __DIR__ . '/../../vendor/autoload.php';
            
            $leitor = IOFactory::createReaderForFile($arquivo['tmp_name']);
            $leitor->setReadDataOnly(true);
            $planilha = $leitor->load($arquivo['tmp_name']);
            $worksheet = $planilha->getActiveSheet();
            
            // Obtém todas as linhas
            $linhas = $worksheet->toArray();
            
            if (empty($linhas)) {
                throw new Exception('A planilha está vazia.');
            }

            // Remove a primeira linha (cabeçalhos)
            $cabecalhos = array_shift($linhas);
            
            // Mapeia os índices das colunas
            $mapa = $this->mapearColunasSISU($cabecalhos);
            
            $alunosImportados = 0;
            $erros = [];
            $todosValidos = true;

            // Processa cada linha
            foreach ($linhas as $index => $linha) {
                $numeroLinha = $index + 2; // +2 porque removemos o cabeçalho e index começa em 0
                
                try {
                    // Valida se a linha tem dados
                    if (empty(array_filter($linha))) {
                        continue; // Pula linhas vazias
                    }

                    // Extrai os dados da linha
                    $dadosAluno = $this->extrairDadosAlunoSISU($linha, $mapa);
                    
                    // Valida os dados
                    $errosValidacao = $this->validarDadosAlunoSISU($dadosAluno);
                    
                    if (!empty($errosValidacao)) {
                        $todosValidos = false;
                        $erros[] = "Linha {$numeroLinha}: " . implode('; ', $errosValidacao);
                        
                        if ($tipoImportacao === 'completa') {
                            throw new Exception("Erro na validação da linha {$numeroLinha}. Importação completa cancelada.");
                        }
                        continue;
                    }

                    // Cria o aluno
                    $this->criarAlunoSISU($dadosAluno);
                    $alunosImportados++;

                } catch (Exception $e) {
                    $todosValidos = false;
                    $erros[] = "Linha {$numeroLinha}: " . $e->getMessage();
                    
                    if ($tipoImportacao === 'completa') {
                        throw new Exception("Erro ao processar linha {$numeroLinha}: " . $e->getMessage());
                    }
                }
            }

            // Se for importação completa e houver erros, cancela tudo
            if ($tipoImportacao === 'completa' && !$todosValidos) {
                DB::rollBack();
                throw new Exception('Importação completa cancelada devido a erros nos dados.');
            }

            // Confirma a transação
            DB::commit();

            // Mensagem de sucesso
            $mensagem = "Importação realizada com sucesso! ";
            $mensagem .= "$alunosImportados aluno(s) importado(s).";
            
            if (!empty($erros) && $tipoImportacao === 'parcial') {
                $mensagem .= " " . count($erros) . " registro(s) com erro foram ignorados.";
            }

            $this->responderJSON([
                'status' => 'sucesso',
                'mensagem' => $mensagem,
                'importados' => $alunosImportados,
                'erros' => $erros
            ]);

        } catch (Exception $e) {
            // Desfaz a transação em caso de erro
            DB::rollBack();
            
            $this->responderJSON([
                'status' => 'erro',
                'mensagem' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mapeia as colunas da planilha SISU
     * 
     * @param array $cabecalhos
     * @return array
     */
    private function mapearColunasSISU(array $cabecalhos): array
    {
        $mapa = [];
        
        foreach ($cabecalhos as $index => $cabecalho) {
            $mapa[trim($cabecalho)] = $index;
        }
        
        return $mapa;
    }

    /**
     * Extrai os dados do aluno da linha da planilha
     * 
     * @param array $linha
     * @param array $mapa
     * @return array
     */
    private function extrairDadosAlunoSISU(array $linha, array $mapa): array
    {
        return [
            // Dados da IES
            'codigo_ies' => $linha[$mapa['CO_IES']] ?? '',
            'nome_ies' => $linha[$mapa['NO_IES']] ?? '',
            'sigla_ies' => $linha[$mapa['SG_IES']] ?? '',
            'campus' => $linha[$mapa['NO_CAMPUS']] ?? '',
            
            // Dados do Curso
            'codigo_curso' => $linha[$mapa['CO_IES_CURSO']] ?? '',
            'nome_curso' => $linha[$mapa['NO_CURSO']] ?? '',
            'turno' => $linha[$mapa['DS_TURNO']] ?? '',
            'formacao' => $linha[$mapa['DS_FORMACAO']] ?? '',
            
            // Dados Pessoais
            'cpf' => $linha[$mapa['NU_CPF_INSCRITO']] ?? '',
            'nome_civil' => $linha[$mapa['NO_INSCRITO']] ?? '',
            'nome_social' => $linha[$mapa['NO_SOCIAL']] ?? '',
            'data_nascimento' => $linha[$mapa['DT_NASCIMENTO']] ?? '',
            'sexo' => $linha[$mapa['TP_SEXO']] ?? '',
            'rg' => $linha[$mapa['NU_RG']] ?? '',
            'cor_raca' => $linha[$mapa['COR_RACA']] ?? '',
            
            // Filiação
            'nome_mae' => $linha[$mapa['NO_MAE']] ?? '',
            
            // Endereço
            'logradouro' => $linha[$mapa['DS_LOGRADOURO']] ?? '',
            'numero' => $linha[$mapa['NU_ENDERECO']] ?? '',
            'complemento' => $linha[$mapa['DS_COMPLEMENTO']] ?? '',
            'bairro' => $linha[$mapa['NO_BAIRRO']] ?? '',
            'cidade' => $linha[$mapa['NO_MUNICIPIO']] ?? '',
            'uf' => $linha[$mapa['SG_UF_INSCRITO']] ?? '',
            'cep' => $linha[$mapa['NU_CEP']] ?? '',
            
            // Contato
            'telefone1' => $linha[$mapa['NU_FONE1']] ?? '',
            'telefone2' => $linha[$mapa['NU_FONE2']] ?? '',
            'email' => $linha[$mapa['DS_EMAIL']] ?? '',
            
            // Notas ENEM
            'nota_linguagens' => $linha[$mapa['NU_NOTA_L']] ?? 0,
            'nota_humanas' => $linha[$mapa['NU_NOTA_CH']] ?? 0,
            'nota_natureza' => $linha[$mapa['NU_NOTA_CN']] ?? 0,
            'nota_matematica' => $linha[$mapa['NU_NOTA_M']] ?? 0,
            'nota_redacao' => $linha[$mapa['NU_NOTA_R']] ?? 0,
            'nota_candidato' => $linha[$mapa['NU_NOTA_CANDIDATO']] ?? 0,
            
            // Matrícula
            'numero_matricula' => $linha[$mapa['DS_MATRICULA']] ?? '',
            'data_matricula' => $linha[$mapa['DT_MATRICULA_EFETIVADA']] ?? '',
            'classificacao' => $linha[$mapa['NU_CLASSIFICACAO']] ?? 0,
            'modalidade_concorrencia' => $linha[$mapa['NO_MODALIDADE_CONCORRENCIA']] ?? '',
            
            // Dados Socioeconômicos
            'ensino_medio' => $linha[$mapa['ENSINO_MEDIO']] ?? '',
            'quilombola' => $linha[$mapa['QUILOMBOLA']] ?? '',
            'pcd' => $linha[$mapa['PCD']] ?? '',
            'renda_familiar' => $linha[$mapa['RENDA_FAMILIAR_BRUTA']] ?? 0,
            'total_membros_familia' => $linha[$mapa['TOTAL_MEMBROS_FAMILIAR']] ?? 0,
        ];
    }

    /**
     * Valida os dados do aluno SISU
     * 
     * @param array $dados
     * @return array
     */
    private function validarDadosAlunoSISU(array $dados): array
    {
        $erros = [];

        // CPF obrigatório e válido
        if (empty($dados['cpf'])) {
            $erros[] = 'CPF é obrigatório';
        } elseif (!Usuario::validarCPF($dados['cpf'])) {
            $erros[] = 'CPF inválido';
        } elseif (Usuario::buscarPorCPF($dados['cpf'])) {
            $erros[] = 'CPF já cadastrado';
        }

        // Nome obrigatório
        if (empty($dados['nome_civil'])) {
            $erros[] = 'Nome é obrigatório';
        }

        // Email obrigatório e válido
        if (empty($dados['email'])) {
            $erros[] = 'Email é obrigatório';
        } elseif (!filter_var($dados['email'], FILTER_VALIDATE_EMAIL)) {
            $erros[] = 'Email inválido';
        } elseif (Usuario::buscarPorEmailPessoal($dados['email'])) {
            $erros[] = 'Email já cadastrado';
        }

        // Data de nascimento obrigatória
        if (empty($dados['data_nascimento'])) {
            $erros[] = 'Data de nascimento é obrigatória';
        }

        // Matrícula obrigatória
        if (empty($dados['numero_matricula'])) {
            $erros[] = 'Número de matrícula é obrigatório';
        }

        return $erros;
    }

    /**
     * Cria um aluno a partir dos dados SISU
     * 
     * @param array $dados
     * @return void
     */
    private function criarAlunoSISU(array $dados): void
    {
        // Mapeia o sexo
        $sexo = match(strtoupper($dados['sexo'])) {
            'M', 'MASCULINO' => UsuarioSexo::MASCULINO,
            'F', 'FEMININO' => UsuarioSexo::FEMININO,
            default => null
        };

        // Mapeia a cor/raça
        $corRaca = match(strtoupper($dados['cor_raca'])) {
            'BRANCA' => UsuarioCorRaca::BRANCA,
            'PRETA' => UsuarioCorRaca::PRETA,
            'PARDA' => UsuarioCorRaca::PARDA,
            'AMARELA' => UsuarioCorRaca::AMARELA,
            'INDÍGENA', 'INDIGENA' => UsuarioCorRaca::INDIGENA,
            default => UsuarioCorRaca::NAO_DECLARADA
        };

        // Mapeia o turno
        $turno = match(strtoupper($dados['turno'])) {
            'MATUTINO', 'MANHÃ', 'MANHA' => Turno::MANHA,
            'VESPERTINO', 'TARDE' => Turno::TARDE,
            'NOTURNO', 'NOITE' => Turno::NOITE,
            'INTEGRAL' => Turno::INTEGRAL,
            default => Turno::INTEGRAL
        };

        // Formata o CPF
        $cpf = preg_replace('/[^0-9]/', '', $dados['cpf']);

        // Formata a data de nascimento
        $dataNascimento = $this->formatarDataSISU($dados['data_nascimento']);

        // Cria o usuário
        $usuario = Usuario::criar([
            'nome_civil' => $dados['nome_civil'],
            'nome_social' => !empty($dados['nome_social']) ? $dados['nome_social'] : null,
            'cpf' => $cpf,
            'rg' => $dados['rg'] ?? null,
            'data_nascimento' => $dataNascimento,
            'sexo' => $sexo,
            'cor_raca' => $corRaca,
            'estado_civil' => UsuarioEstadoCivil::SOLTEIRO,
            'nacionalidade' => UsuarioNacionalidade::BRASILEIRA,
            'naturalidade' => $dados['cidade'] ?? null,
            'email_pessoal' => $dados['email'],
        ]);
        $usuario->save();

        // Cria o contato
        $telefone = preg_replace('/[^0-9]/', '', $dados['telefone1']);
        
        $usuarioContato = UsuarioContato::criar([
            'usuario_id' => $usuario->obterId(),
            'cep' => preg_replace('/[^0-9]/', '', $dados['cep']),
            'endereco' => $dados['logradouro'],
            'numero' => $dados['numero'],
            'complemento' => $dados['complemento'] ?? null,
            'bairro' => $dados['bairro'],
            'cidade' => $dados['cidade'],
            'uf' => UF::fromName($dados['uf']) ?? UF::SP,
            'telefone_celular' => $telefone,
        ]);
        $usuarioContato->save();

        // Cria o aluno
        $aluno = Aluno::criar([
            'usuario_id' => $usuario->obterId()
        ]);
        $aluno->save();

        // Cria o responsável (mãe)
        if (!empty($dados['nome_mae'])) {
            $responsavel = AlunoResponsavel::criar([
                'aluno_id' => $aluno->obterId(),
                'nome' => $dados['nome_mae'],
                'tipo' => AlunoResponsavelTipo::MAE
            ]);
            $responsavel->save();
        }

        // TODO: Buscar ou criar o curso baseado no código/nome
        
        // Formata a data de matrícula
        $dataMatricula = $this->formatarDataSISU($dados['data_matricula']);

        // Cria a matrícula
        if (!empty($dados['numero_matricula']) && !empty($dataMatricula)) {
            $alunoMatricula = AlunoMatricula::criar([
                'aluno_id' => $aluno->obterId(),
                // 'matriz_id' => $matriz->obterId(), // TODO: Definir a matriz correta
                'matricula' => $dados['numero_matricula'],
                'data_matricula' => $dataMatricula,
            ]);
            $alunoMatricula->salvar();
        }
    }

    /**
     * Formata uma data do formato SISU para o formato do banco
     * 
     * @param string $data
     * @return string|null
     */
    private function formatarDataSISU(string $data): ?string
    {
        if (empty($data)) {
            return null;
        }

        // Formatos de data possíveis
        $formatos = ['d/m/Y', 'Y-m-d', 'd-m-Y', 'Y/m/d'];
        
        foreach ($formatos as $formato) {
            $dataObj = DateTime::createFromFormat($formato, $data);
            if ($dataObj !== false) {
                return $dataObj->format('Y-m-d');
            }
        }

        return null;
    }
}