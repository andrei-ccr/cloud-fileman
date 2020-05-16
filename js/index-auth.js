import {Login, Register, LoginAsGuest} from './modules/auth.js';
import {ValidateInput} from './modules/auth.js';

let btnLogin = '#btn-enter'; //Login button
let btnRegister = '#btn-register'; //Register button
let btnLoginGuest = '#btn-no-acc'; //Login as guest button 

$(document).on('click', btnLogin, function(e) {

    let EmailInput = $("#email").val();
    let PassInput = $("#pass").val();

    if(ValidateInput(EmailInput, PassInput)) {
        Login(EmailInput, PassInput);
    }
    
});

$(document).on('click', btnRegister, function(e) {

    let EmailInput = $("#email").val();
    let PassInput = $("#pass").val();

    if(ValidateInput(EmailInput, PassInput)) {
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