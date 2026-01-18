// Función que hashea un texto usando SHA-256 y devuelve el resultado en formato hexadecimal.
// Se usa para evitar enviar la contraseña real al servidor.
async function sha256(message) {
    // Convierte el texto en un buffer de bytes
    const msgBuffer = new TextEncoder().encode(message);

    // Aplica SHA-256 utilizando la API de criptografía del navegador
    const hashBuffer = await crypto.subtle.digest('SHA-256', msgBuffer);

    // Convierte el buffer de bytes resultante en un array normal
    const hashArray = Array.from(new Uint8Array(hashBuffer));

    // Cada byte se transforma en su representación hexadecimal (00–ff)
    return hashArray.map(b => b.toString(16).padStart(2, '0')).join('');
}

// Hasheo de contraseña en el formulario de REGISTRO
// Obtiene el formulario de registro si existe en la página
const registerForm = document.getElementById("register-form");

if (registerForm) {
    registerForm.addEventListener("submit", async (e) => {

        // Campo de contraseña del formulario
        const passInput = document.getElementById("password");
        const original = passInput.value;  // Contraseña escrita por el usuario

        // Solo actúa si la contraseña no está vacía
        if (original.length > 0) {
            e.preventDefault(); // Evita el envío normal

            // Genera el hash SHA-256 de la contraseña
            const hashed = await sha256(original);

            // Sustituye la contraseña original por el hash generado
            passInput.value = hashed;

            // Reenvía el formulario con el valor ya hasheado
            registerForm.submit();
        }
    });
}

// Hasheo de contraseña en el formulario de LOGIN
// Obtiene el formulario de login si existe
const loginForm = document.getElementById("login-form");

if (loginForm) {
    loginForm.addEventListener("submit", async (e) => {

        const passInput = document.getElementById("password");
        const original = passInput.value;

        // Si el usuario escribió una contraseña, se procede al hash
        if (original.length > 0) {
            e.preventDefault(); // Evita el envío con la contraseña real

            const hashed = await sha256(original); // Hash SHA-256
            passInput.value = hashed; // Sustituye el valor

            loginForm.submit(); // Envía el formulario ya protegido
        }
    });
}