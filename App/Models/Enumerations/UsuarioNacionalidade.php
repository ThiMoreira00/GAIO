<?php

/**
 * @file UsuarioNacionalidade.php
 * @description Enumeração das nacionalidades dos usuários
 * @author Thiago Moreira
 * @copyright Copyright (c) 2025
 */

namespace App\Models\Enumerations;

/**
 * Enumeração de nacionalidades dos usuários
 * 
 * @package App\Models\Enumerations
 */
enum UsuarioNacionalidade: string
{
    case BRASILEIRA = "Brasileira (Brasil)";
    case PORTUGUESA = "Portuguesa (Portugal)";
    case AFEGAA = "Afegãa (Afeganistão)";
    case ALBANESA = "Albanesa (Albânia)";
    case ALEMA = "Alemã (Alemanha)";
    case ANDORRANA = "Andorrana (Andorra)";
    case ANGOLANA = "Angolana (Angola)";
    case ANGUILANA = "Anguilana (Anguila)";
    case ANTIGUANA = "Antiguana (Antígua e Barbuda)";
    case ANTILHANA = "Antilhana (Antilhas Holandesas)";
    case ARABE_SAUDITA = "Árabe-Saudita (Arábia Saudita)";
    case ARGELINA = "Argelina (Argélia)";
    case ARGENTINA = "Argentina (Argentina)";
    case ARMENIA = "Arménia (Armênia)";
    case ARUBANA = "Arubana (Aruba)";
    case AUSTRALIANA = "Australiana (Austrália)";
    case AUSTRIACA = "Austríaca (Áustria)";
    case AZERI = "Azeri (Azerbaijão)";
    case BAAMIANA = "Baamiana (Bahamas)";
    case BANGLADECHIANA = "Bangladechiana (Bangladesh)";
    case BARBADENSE = "Barbadense (Barbados)";
    case BARENITA = "Barenita (Barém)";
    case BELGA = "Belga (Bélgica)";
    case BELIZENHA = "Belizenha (Belize)";
    case BENINENSE = "Beninense (Benim)";
    case BERMUDENSE = "Bermudense (Bermudas)";
    case BIELORRUSSA = "Bielorrussa (Bielorrússia)";
    case BIRMANESA = "Birmanesa (MyanmarMyanmar)";
    case BOLIVIANA = "Boliviana (Bolívia)";
    case BOTSUANA = "Botsuana (Botswana)";
    case BRITANICA = "Britânica (Reino Unido)";
    case BRUNEINA = "Bruneína (Brunei)";
    case BURQUINA = "Burquina (Burquina Fasso)";
    case BURUNDIANA = "Burundiana (Burundi)";
    case BUTANESA = "Butanesa (Butão)";
    case BOSNIA = "Bósnia (Bósnia e Herzegovina)";
    case BULGARA = "Búlgara (Bulgária)";
    case CABO_VERDIANA = "Cabo-Verdiana (Cabo Verde)";
    case CAIMANESA = "Caimanesa (Ilhas Caimã)";
    case CAMARONESA = "Camaronesa (Camarões)";
    case CAMBOJANA = "Cambojana (Camboja)";
    case CANADIANA = "Canadiana (Canadá)";
    case CATARIANA = "Catariana (Catar)";
    case CAZAQUE = "Cazaque (Cazaquistão)";
    case CENTRO_AFRICANA = "Centro-Africana (República Centro-Africana)";
    case CHADIANA = "Chadiana (Chade)";
    case CHILENA = "Chilena (Chile)";
    case CHINA = "China (China)";
    case CINGALESA = "Cingalesa (Sri Lanka)";
    case CIPRIOTA = "Cipriota (Chipre)";
    case COLOMBIANA = "Colombiana (Colômbia)";
    case COMORIANA = "Comoriana (Comores)";
    case CONGOLESA = "Congolesa (República do Congo)";
    case COOKENSE = "Cookense (Ilhas Cook)";
    case COSTARRIQUENHA = "Costarriquenha (Costa Rica)";
    case CROATA = "Croata (Croácia)";
    case CUBANA = "Cubana (Cuba)";
    case CURACAUENSE = "Curaçauense (Curaçau)";
    case DINAMARQUESA = "Dinamarquesa (Dinamarca)";
    case DOMINICANA = "Dominicana (República Dominicana)";
    case DOMINIQUA = "Dominiqua (Dominica)";
    case EGIPCIA = "Egípcia (Egito)";
    case EMIRADENSE = "Emiradense (Emirados Árabes Unidos)";
    case EQUATORIANA = "Equatoriana (Equador)";
    case ERITREIA = "Eritreia (Eritreia)";
    case ESCOCESA = "Escocesa (Escócia)";
    case ESLOVACA = "Eslovaca (Eslováquia)";
    case ESLOVENA = "Eslovena (Eslovênia)";
    case ESPANHOLA = "Espanhola (Espanha)";
    case ESSUATINIANOA = "Essuatinianoa (Essuatíni)";
    case ESTONIANA = "Estoniana (Estónia)";
    case ETIOPE = "Etíope (Etiópia)";
    case FAROENSE = "Faroense (Ilhas Feroé)";
    case FIJIANA = "Fijiana (Fiji)";
    case FILIPINA = "Filipina (Filipinas)";
    case FINLANDESA = "Finlandesa (Finlândia)";
    case FRANCESA = "Francesa (França)";
    case GABONESA = "Gabonesa (Gabão)";
    case GALESA = "Galesa (País de Gales)";
    case GAMBIANA = "Gambiana (Gâmbia)";
    case GANESA = "Ganesa (Gana)";
    case GEORGIANA = "Georgiana (Geórgia)";
    case GRANADINA = "Granadina (Granada)";
    case GREGA = "Grega (Grécia)";
    case GUADALUPENSE = "Guadalupense (Guadalupe)";
    case GUAMESA = "Guamesa (Guam)";
    case GUATEMALTECA = "Guatemalteca (Guatemala)";
    case GUIANENSE = "Guianense (Guiana Francesa)";
    case GUIANESA = "Guianesa (Guiana)";
    case GUINEANA = "Guineana (Guiné)";
    case GUINEENSE = "Guineense (Guiné-Bissau)";
    case GUINEU_EQUATORIANA = "Guinéu-Equatoriana (Guiné Equatorial)";
    case HAITIANA = "Haitiana (Haiti)";
    case HONCONGUESA = "Honconguesa (Hong Kong)";
    case HONDURENHA = "Hondurenha (Honduras)";
    case HUNGARA = "Húngara (Hungria)";
    case IEMENITA = "Iemenita (Iêmen)";
    case INDIANA = "Indiana (Índia)";
    case INDONESIA = "Indonésia (Indonésia)";
    case INGLESA = "Inglesa (Inglaterra)";
    case IRANIANA = "Iraniana (IrãIrão)";
    case IRAQUIANA = "Iraquiana (Iraque)";
    case IRLANDESA = "Irlandesa (Irlanda)";
    case ISLANDA = "Islanda (Islândia)";
    case ISRAELENSE = "Israelense (Israel)";
    case ITALIANA = "Italiana (Itália)";
    case JAMAICANA = "Jamaicana (Jamaica)";
    case JAPONESA = "Japonesa (Japão)";
    case JIBUTIANA = "Jibutiana (Djibuti)";
    case JORDANA = "Jordana (Jordânia)";
    case KOSOVAR = "Kosovar (Kosovo)";
    case KUWAITIANA = "Kuwaitiana (Kuwait)";
    case LAOCIANA = "Laociana (LaosLaos)";
    case LESOTIANA = "Lesotiana (Lesoto)";
    case LETA = "Letã (Letónia)";
    case LIBANESA = "Libanesa (Líbano)";
    case LIBERIANA = "Liberiana (Libéria)";
    case LIBIA = "Líbia (Líbia)";
    case LISTENSTAINIANA = "Listenstainiana (Liechtenstein)";
    case LITUANA = "Lituana (Lituânia)";
    case LUXEMBURGUESA = "Luxemburguesa (Luxemburgo)";
    case MACAENSE = "Macaense (Macau)";
    case MACEDONICA = "Macedônica (Macedónia do NorteMacedônia do Norte)";
    case MALAIA = "Malaia (Malásia)";
    case MALAUIANA = "Malauiana (Malawi)";
    case MALDIVA = "Maldiva (Maldivas)";
    case MALGAXE = "Malgaxe (Madagáscar)";
    case MALINES = "Malinês (Mali)";
    case MALTESA = "Maltesa (Malta)";
    case MARFINENSE = "Marfinense (Costa do Marfim)";
    case MARROQUINA = "Marroquina (Marrocos)";
    case MARTINICANA = "Martinicana (Martinica)";
    case MAURICIANA = "Mauriciana (Ilhas Maurícias)";
    case MAURITANA = "Mauritana (Mauritânia)";
    case MEXICANA = "Mexicana (México)";
    case MICRONESIA = "Micronésia (Estados Federados da Micronésia)";
    case MOLDAVA = "Moldava (Moldávia)";
    case MONEGASCA = "Monegasca (Mónaco)";
    case MONGOL = "Mongol (MongóliaMongólia)";
    case MONSERRATENSE = "Monserratense (Monserrate)";
    case MONTENEGRINA = "Montenegrina (Montenegro)";
    case MOCAMBICANA = "Moçambicana (Moçambique)";
    case NAMIBIANA = "Namibiana (Namíbia)";
    case NAURUANA = "Nauruana (Nauru)";
    case NEERLANDESA = "Neerlandesa (Países Baixos)";
    case NEOCALEDONIA = "Neocaledónia (Nova Caledônia)";
    case NEOZELANDESA = "Neozelandesa (Nova Zelândia)";
    case NEPALESA = "Nepalesa (Nepal)";
    case NICARAGUENSE = "Nicaraguense (Nicarágua)";
    case NIGERIANA = "Nigeriana (Nigéria)";
    case NIGERINA = "Nigerina (Níger)";
    case NORTE_AMERICANA = "Norte-Americana (Estados Unidos)";
    case NORTE_COREANA = "Norte-Coreana (Coreia do Norte)";
    case NORTE_IRLANDESA = "Norte-Irlandesa (Irlanda do Norte)";
    case NORUEGUESA = "Norueguesa (Noruega)";
    case OMANENSE = "Omanense (OmãOmã)";
    case PALAUANA = "Palauana (Palau)";
    case PALESTINIANA = "Palestiniana (Palestina)";
    case PANAMENHA = "Panamenha (Panamá)";
    case PAPUA = "Papua (Papua-Nova Guiné)";
    case PAQUISTANESA = "Paquistanesa (Paquistão)";
    case PARAGUAIA = "Paraguaia (Paraguai)";
    case PERUANA = "Peruana (Peru)";
    case POLINESIA = "Polinésia (Polinésia Francesa)";
    case POLONESA = "Polonesa (Polónia)";
    case PORTO_RIQUENHA = "Porto-Riquenha (Porto Rico)";
    case QUENIANA = "Queniana (Quênia)";
    case QUIRGUIZ = "Quirguiz (Quirguistão)";
    case QUIRIBATIANA = "Quiribatiana (Kiribati)";
    case ROMENA = "Romena (Roménia)";
    case RUANDESA = "Ruandesa (Ruanda)";
    case RUSSA = "Russa (Rússia)";
    case SALOMONENSE = "Salomonense (Ilhas Salomão)";
    case SALVADORENHA = "Salvadorenha (El Salvador)";
    case SAMOANA = "Samoana (Samoa)";
    case SAMOENSE = "Samoense (Samoa Americana)";
    case SANTA_LUCIENSE = "Santa-Luciense (Santa Lúcia)";
    case SEICHELENSE = "Seichelense (Seicheles)";
    case SENEGALESA = "Senegalesa (Senegal)";
    case SERRA_LEONESA = "Serra-Leonesa (Serra Leoa)";
    case SINGAPURENSE = "Singapurense (Singapura)";
    case SOMALI = "Somali (Somália)";
    case SUDANESA = "Sudanesa (Sudão)";
    case SUECA = "Sueca (Suécia)";
    case SUL_AFRICANA = "Sul-Africana (África do Sul)";
    case SUL_COREANA = "Sul-Coreana (Coreia do Sul)";
    case SURINAMESA = "Surinamesa (Suriname)";
    case SUICA = "Suíça (Suíça)";
    case SAO_CRISTOVENSE = "São-Cristovense (São Cristóvão e Neves)";
    case SAO_MARINHENSE = "São-Marinhense (San Marino)";
    case SAO_MARTINHENSE = "São-Martinhense (São Martinho)";
    case SAO_TOMENSE = "São-Tomense (São Tomé e Príncipe)";
    case SAO_VICENTINA = "São-Vicentina (São Vicente e Granadinas)";
    case SERVIA = "Sérvia (Sérvia)";
    case SIRIA = "Síria (Síria)";
    case TAILANDESA = "Tailandesa (Tailândia)";
    case TAIWANESA = "Taiwanesa (Taiwan)";
    case TAJIQUE = "Tajique (Tajiquistão)";
    case TANZANIANA = "Tanzaniana (Tanzânia)";
    case TCHECA = "Tcheca (Chéquia)";
    case TIMORENSE = "Timorense (Timor-Leste)";
    case TOGOLESA = "Togolesa (Togo)";
    case TONGANESA = "Tonganesa (Tonga)";
    case TRINITARIA_TOBAGENSE = "Trinitária-Tobagense (Trinidad e Tobago)";
    case TUNISIANA = "Tunisiana (Tunísia)";
    case TURCA = "Turca (Turquia)";
    case TURQUEMENA = "Turquemena (Turquemenistão)";
    case TURQUENSE = "Turquense (Turcas e Caicos)";
    case TUVALUANA = "Tuvaluana (Tuvalu)";
    case UCRANIANA = "Ucraniana (Ucrânia)";
    case UGANDENSE = "Ugandense (Uganda)";
    case URUGUAIA = "Uruguaia (Uruguai)";
    case USBEQUE = "Usbeque (Uzbequistão)";
    case VANUATUENSE = "Vanuatuense (Vanuatu)";
    case VATICANA = "Vaticana (Vaticano)";
    case VENEZUELANA = "Venezuelana (Venezuela)";
    case VIETNAMITA = "Vietnamita (Vietname)";
    case VIRGINENSE_AMERICANAS = "Virginense (Ilhas Virgens Americanas)";
    case VIRGINENSE_BRITANICAS = "Virginense (Ilhas Virgens Britânicas)";
    case ZAMBIANA = "Zambiana (Zâmbia)";
    case ZIMBABUANA = "Zimbabuana (Zimbabwe)";

    /**
     * Função para obter por meio da chave (nome)
     *
     * @param string $name
     * @return ?self
     */
    public static function fromName(string $name): ?self
    {
        // Usa a função constant para recuperar o case
        if (defined("self::{$name}")) {
            return constant("self::{$name}");
        }
        return null;
    }
}