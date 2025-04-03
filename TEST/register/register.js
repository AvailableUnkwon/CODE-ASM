const Username = document.getElementById('email');

const Password = document.getElementById('password');

function signup() {
    if (Username.value == "" || Password.value == "") {
        alert("Please fill in the required fields");
    }
    else{
        const user = {
            email: Username.value,
            password: Password.value
        };
        let json = JSON.stringify(user);
        localStorage.setItem(Username.value, json);
        alert("You have successfully registered");
        window.open("../login/login.html");
    }
}


