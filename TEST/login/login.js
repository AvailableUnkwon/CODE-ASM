const Username = document.getElementById('email');

const Password = document.getElementById('password');

function login() {
    if (Username.value == "" || Password.value == "") {
        alert("Please fill in the required fields");
    }
    else {
        const user = JSON.parse(localStorage.getItem(Username.value));
        if (!user) {
            alert("Account does not exist");
        } else if (
            user.email == Username.value &&
            user.password == Password.value
        ) {
            window.open("../main/main.html");
        }
        else {
            alert("Wrong email or password"); 
        }
    }
}