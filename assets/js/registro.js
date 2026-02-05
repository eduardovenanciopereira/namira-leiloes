document.addEventListener("DOMContentLoaded", () => {
    document.addEventListener("click", (event) => {
        const target = event.target;
        
        const formPassword = document.getElementById("line-password");

        if (target.classList.contains("password-account")) {
            formPassword.classList.add("active");
        } else {
            formPassword.classList.remove("active");
        }
        
        const formNumberPhone = document.getElementById("line-number-phone")
        
        if (target.classList.contains("number-phone")) {
            formNumberPhone.classList.add("active");
        } else {
            formNumberPhone.classList.remove("active");
        }
    })
    
    const viewPassword = document.getElementById("view-password");
    
    viewPassword.addEventListener("click", () => {
        const passwordAccountInput = document.getElementById("password-account");
        
        viewPassword.classList.toggle("active");
        if (viewPassword.classList.contains("active")) {
            viewPassword.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="122.879" height="79.699" viewBox="0 0 122.879 79.699"><g><path d="M0.955 37.326c2.922-3.528 5.981-6.739 9.151-9.625C24.441 14.654 41.462 7.684 59.01 7.334c6.561-.131 13.185.665 19.757 2.416l-5.904 5.904c-4.581-.916-9.168-1.324-13.714-1.233-15.811.316-31.215 6.657-44.262 18.533-2.324 2.115-4.562 4.39-6.702 6.82 4.071 4.721 8.6 8.801 13.452 12.227 2.988 2.111 6.097 3.973 9.296 5.586l-5.262 5.262c-2.782-1.504-5.494-3.184-8.12-5.039C11.408 53.472 5.738 48.181.771 41.96c-1.109-1.397-.999-3.37.184-4.634ZM96.03 0l5.893 5.893-73.804 73.806-5.894-5.895L96.03 0Zm1.69 17.609c4.423 2.527 8.767 5.528 12.994 9.014 3.877 3.196 7.635 6.773 11.24 10.735 1.163 1.277 1.22 3.171.226 4.507-4.131 5.834-8.876 10.816-14.069 14.963-12.992 10.371-28.773 15.477-44.759 15.549-6.114.027-9.798-3.141-15.825-4.576l3.545-3.543c4.065.705 8.167 1.049 12.252 1.031 14.421-.064 28.653-4.668 40.366-14.02 3.998-3.191 7.706-6.939 11.028-11.254-2.787-2.905-5.627-5.543-8.508-7.918-4.455-3.673-9.042-6.759-13.707-9.273l5.217-5.215ZM61.44 18.143c2.664 0 5.216.481 7.576 1.359l-5.689 5.689a14.94 14.94 0 0 0-1.886-.119c-4.081 0-7.775 1.654-10.449 4.328-2.674 2.674-4.328 6.369-4.328 10.45 0 .639.04 1.268.119 1.885l-5.689 5.691a21.71 21.71 0 0 1-1.359-7.576c0-5.995 2.43-11.42 6.358-15.349 3.928-3.929 9.354-6.358 15.348-6.358ZM82.113 33.216c.67 2.09 1.032 4.32 1.032 6.634 0 5.994-2.43 11.42-6.357 15.348-3.929 3.928-9.355 6.357-15.348 6.357-2.313 0-4.542-.361-6.633-1.033l5.914-5.914c.238.012.478.018.719.018 4.081 0 7.775-1.652 10.449-4.326 2.674-2.674 4.328-6.369 4.328-10.449 0-.241-.006-.48-.018-.72l5.914-5.915Z"/></g></svg>';
            
            passwordAccountInput.type = "text";
        } else {
            viewPassword.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 122.88 65.06"><g><path d="M0.95 30.01c2.92-3.53 5.98-6.74 9.15-9.63C24.44 7.33 41.46.36 59.01.01c17.51-.35 35.47 5.9 51.7 19.29 3.88 3.2 7.63 6.77 11.24 10.74 1.16 1.28 1.22 3.17.23 4.51-4.13 5.83-8.88 10.82-14.07 14.96C95.12 59.88 79.34 64.98 63.35 65.06c-15.93.07-32.06-4.86-45.8-14.57-6.14-4.34-11.81-9.63-16.78-15.85-1.11-1.4-1-3.37.18-4.63ZM61.44 26.46c.59 0 1.17.09 1.71.24-.46.5-.73 1.17-.73 1.9 0 1.56 1.26 2.82 2.82 2.82.77 0 1.46-.3 1.97-.8.2.6.3 1.24.3 1.9 0 3.35-2.72 6.07-6.07 6.07s-6.07-2.72-6.07-6.07 2.72-6.06 6.07-6.06ZM61.44 10.82c5.99 0 11.42 2.43 15.35 6.36 3.93 3.93 6.36 9.35 6.36 15.35 0 5.99-2.43 11.42-6.36 15.35-3.93 3.93-9.35 6.36-15.35 6.36-5.99 0-11.42-2.43-15.35-6.36-3.93-3.93-6.36-9.35-6.36-15.35 0-5.99 2.43-11.42 6.36-15.35 3.93-3.93 9.36-6.36 15.35-6.36ZM71.89 22.08c-2.67-2.67-6.37-4.33-10.45-4.33-4.08 0-7.78 1.65-10.45 4.33-2.67 2.67-4.33 6.37-4.33 10.45 0 4.08 1.65 7.78 4.33 10.45 2.67 2.67 6.37 4.33 10.45 4.33 4.08 0 7.78-1.65 10.45-4.33 2.67-2.67 4.33-6.37 4.33-10.45 0-4.08-1.66-7.78-4.33-10.45ZM14.89 25.63c-2.32 2.11-4.56 4.39-6.7 6.82 4.07 4.72 8.6 8.8 13.45 12.23 12.54 8.85 27.21 13.35 41.69 13.29 14.42-.07 28.65-4.67 40.37-14.02 4-3.19 7.7-6.94 11.03-11.25-2.79-2.91-5.63-5.54-8.51-7.92C91.33 12.51 75 6.79 59.15 7.1 43.34 7.42 27.93 13.76 14.89 25.63Z"/></g></svg>';
            
            passwordAccountInput.type = "password";
        }
    });
    
    const numberPhoneInput = document.getElementById("number-phone");

    numberPhoneInput.addEventListener("input", (e) => {
        let input = e.target;
    
        // Posição atual do cursor
        let cursorPosition = input.selectionStart;
    
        // Valor atual
        let value = input.value;
    
        // Remove tudo que não é número
        let numbers = value.replace(/\D/g, "");
    
        // Limita a 11 dígitos (DDD + número)
        numbers = numbers.substring(0, 11);
    
        // Formata número
        let formatted = "";
        if (numbers.length > 0) {
            formatted += "(" + numbers.substring(0, Math.min(2, numbers.length));
        }
        if (numbers.length >= 3) {
            formatted += ") " + numbers.substring(2, Math.min(7, numbers.length));
        }
        if (numbers.length >= 8) {
            formatted += "-" + numbers.substring(7, 11);
        }
    
        // Calcula diferença de tamanho para ajustar cursor
        let diff = formatted.length - value.length;
    
        input.value = formatted;
    
        // Ajusta cursor, evitando que vá pro final
        let newPosition = cursorPosition + diff;
    
        // Corrige se cursor ficar fora do campo
        if (newPosition > formatted.length) newPosition = formatted.length;
        if (newPosition < 0) newPosition = 0;
    
        input.setSelectionRange(newPosition, newPosition);
    });
    
    const btnEnter = document.getElementById("btn-enter");
    
    btnEnter.addEventListener("click", async (e) => {
        e.preventDefault();
        
        const namePersonInput = document.getElementById("name-person").value;
        const emailAccountInput = document.getElementById("email-account").value;
        const passwordAccountInput = document.getElementById("password-account").value;
        let numberPhoneInput = document.getElementById("number-phone").value;
        
        const returnMessage = document.getElementById("return-message");
        
        if (returnMessage && returnMessage.classList.contains("active")) {
            returnMessage.classList.remove("active");
        }

        const responseRequest = await fetch("http://localhost:7777/api/registro", {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify({
                name_person: namePersonInput,
                email_account: emailAccountInput,
                password_account: passwordAccountInput,
                number_phone: numberPhoneInput
            }),
            credentials: "include"
        });
        const resultJson = await responseRequest.json();
        
        const messageServer = resultJson.message;
        
        if(!responseRequest.ok) {
            if (!returnMessage.classList.contains("active")) {
                returnMessage.classList.add("active");
            }
            
            returnMessage.innerHTML = `<span class="message">${messageServer}</span>`;
        } else {
            if (!returnMessage.classList.contains("active")) {
                returnMessage.classList.add("active");
            }
            
            returnMessage.classList.add("success");
            returnMessage.innerHTML = `<span class="message">${messageServer}</span>`;
            
            const lastPage = localStorage.getItem("lastPage");
            if (lastPage) {
                setTimeout(function() {
                    window.location.href = lastPage;
                }, 3000);
            } else {
                setTimeout(function() {
                    window.location.href = "/";
                }, 3000);
            }
        }
    })
});