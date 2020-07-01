import {Login, Register, LoginAsGuest } from './modules/auth.js';
import {ValidateInput, ValidateInputRegister} from './modules/auth.js';

let btnLogin = '#btn-enter'; //Login button
let btnRegister = '#btn-register'; //Register button
let btnToRegister = '#btn-to-register'; //Go to Register page button
let btnLoginGuest = '#btn-no-acc'; //Login as guest button 

$(document).on('click', btnLogin, function(e) {

    let EmailInput = $("#email").val();
    let PassInput = $("#pass").val();

    if(ValidateInput(EmailInput, PassInput)) {
        Login(EmailInput, PassInput);
    }
    
});

$(document).on('click', btnToRegister, function(e) {
    window.location.href = "/register";
});

$(document).on('click', btnRegister, function(e) {

    let EmailInput = $("#email").val();
    let ConfirmEmailInput = $("#cemail").val();
    let UsernameInput = $("#username").val();
    let ConfirmPassInput = $("#cpass").val();
    let PassInput = $("#pass").val();

    if(ValidateInputRegister(EmailInput, ConfirmEmailInput, PassInput, ConfirmPassInput, UsernameInput)) {
        Register(EmailInput, PassInput);
    }
    
});

$(document).on('click', btnLoginGuest, function(e) {
    LoginAsGuest();
});

$(document).on('keypress', function(e) {
    
    let key = e.which;
	if(key == 13) {

		let EmailInput = $("#email").val();
        let PassInput = $("#pass").val();

        if(ValidateInput(EmailInput, PassInput)) {
            Login(EmailInput, PassInput);
        }
        
	}
});