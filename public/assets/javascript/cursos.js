async function obterCurso(cursoId) {
    const response = await $.ajax({
        url: `/cursos/${cursoId}/obter`,
        method: 'GET',
        dataType: 'json',
        success: function(response) {
            console.log('Curso obtido:', response);
            return response;
        },
        error: function(xhr, status, error) {
            console.error('Erro ao obter o curso:', error);
            return null;
        }
    });

    return response;
}