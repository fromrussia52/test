document.body.onload = function () {
    var xhr = new XMLHttpRequest();
    xhr.open('GET', '/api/isauth');
    xhr.onload = function (e) {
        var authForm = document.querySelector('div.auth');
        var billingForm = document.querySelector('div.billing');
        if (e.target.response === 'true') {
            if (authForm) {
                authForm.classList.add('hidden');
            }
            if (billingForm) {
                billingForm.classList.remove('hidden');
            }

            getBalans();
        } else {
            if (authForm) {
                authForm.classList.remove('hidden');
            }
            if (billingForm) {
                billingForm.classList.add('hidden');
            }
        }
    }
    xhr.send();
}

function login() {
    var xhr = new XMLHttpRequest();
    xhr.open('POST', '/api/login');
    var data = new FormData();
    var loginDom = document.querySelector('div.login input');
    var login = (loginDom && loginDom.value) || null;
    var passwordDom = document.querySelector('div.password input');
    var password = (passwordDom && passwordDom.value) || null;
    data.append('login', login);
    data.append('password', password);
    xhr.onload = function (e) {
        if (e.target.status === 200) {
            var authForm = document.querySelector('div.auth');
            if (authForm) {
                authForm.classList.add('hidden');
            }
            var billingForm = document.querySelector('div.billing');
            if (billingForm) {
                billingForm.classList.remove('hidden');
            }
        } else {
            alert(e.target.response);
        }
    }
    xhr.send(data);
}

function logout() {
    var xhr = new XMLHttpRequest();
    xhr.open('GET', '/api/logout');
    xhr.onload = function (e) {
        var authForm = document.querySelector('div.auth');
        var billingForm = document.querySelector('div.billing');
        if (e.target.response === 'true') {
            if (authForm) {
                authForm.classList.remove('hidden');
            }
            if (billingForm) {
                billingForm.classList.add('hidden');
            }
        } else {
            if (authForm) {
                authForm.classList.add('hidden');
            }
            if (billingForm) {
                billingForm.classList.remove('hidden');
            }
        }
    }
    xhr.send();
}

function getBalans(){
    //get current balans
    var xhr = new XMLHttpRequest();
    xhr.open('GET', '/api/balans');
    xhr.onload = function (e) {
        if(e.target.status === 200){
            var balansValueDom = document.querySelector('span.balans_value');
            if (balansValueDom) {
                balansValueDom.innerHTML = e.target.response;
            }
        } else {
            alert(e.target.response);
        }
    }
    xhr.send();
}

function pulloff() { }