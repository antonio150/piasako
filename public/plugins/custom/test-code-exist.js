function testExistCodeEntity(inputCode, formulaire, codeList) {
    formulaire.submit(function (event) {
        event.preventDefault(); // empêcher l'action par défaut du formulaire
        const inputCodeValue = inputCode.val(); // récupérer la valeur de l'inputCode

        // validation test
        if(codeList.includes(inputCodeValue)){
            toastr.error("Le code  saisie existe déjà");
            inputCode.addClass('is-invalid');
        }else{
            $(formulaire).unbind('submit').submit(); // lancer le submit si le test est valide
        }
    })
}