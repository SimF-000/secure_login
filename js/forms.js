function formhash(form, password) {
    var p = document.createElement("input");
     form.appendChild(p);
    p.name = "p";
    p.type = "hidden";
    p.value = hex_sha512(password.value);
 
    // on vérifie que le password non hash n'est pas envoyé. 
    password.value = "";
 
    // evoie du formulaire
    form.submit();
}
 
function regformhash(form, email, password, conf) {
    if (  email.value == ''     || 
          password.value == ''  || 
          conf.value == '') {
 
        alert('Vous n\'avez pas remplis tous les champs');
        return false;
    }
 
    // on vérifie que les champs sont correctement remplis
    if (password.value.length < 6) {
        alert('Le mot de passe doit avoir au moins 6 characteres');
        form.password.focus();
        return false;
    }
  
    var re = /(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{6,}/; 
    if (!re.test(password.value)) {
        alert('Le mot de passe doit contenir au moins une majuscule, un nombre et une minuscule');
        return false;
    }
 
    if (password.value != conf.value) {
        alert('le mot de passe rentré ne correspond pas à la confirmation');
        form.password.focus();
        return false;
    }
 
    var p = document.createElement("input");
    form.appendChild(p);
    p.name = "p";
    p.type = "hidden";
    p.value = hex_sha512(password.value);
 
    password.value = "";
    conf.value = "";
 
    form.submit();
    return true;
}