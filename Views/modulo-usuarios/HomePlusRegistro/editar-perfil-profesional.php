<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Editar Perfil - Profesional | Home+</title>
    <link rel="stylesheet" href="/Views/modulo-usuarios/HomePlusRegistro/css/editar-perfil-profesional.css">
    <link rel="icon" href="/Views/assets/img/1. Usuario/homelogo.jpg">
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>

    <style>
    /* Fuente */
    @font-face {
      font-family: "MadaniArabic";
      src: url("./Views/fonts/MadaniArabicDEMO-Regular.otf") format("opentype");
      font-weight: 400;
    }

    @font-face {
      font-family: "MadaniArabic";
      src: url("./Views/fonts/MadaniArabicDEMO-Bold.otf") format("opentype");
      font-weight: 700;
    }

    .font-madani {
      font-family: "MadaniArabic", sans-serif;
    }
    </style>
</head>

<nav
    class="sticky top-0 z-50 w-auto bg-white/30 backdrop-blur-md border-b border-white/20 flex justify-center     items-center shadow-lg xl:mx-60 rounded-b-xl">
    <img src="/Views/assets/img/Logo/Logo-Home-Transparente.svg" alt="Logo de Home_Plus" class="w-40 p-4">
</nav>

<body class="font-madani bg-[#f5f1eb] m-0 p-0 ">
    <section class="xl:mx-60 my-16 bg-white p-8 rounded-2xl shadow-md bg-gray-50 border border-gray-200 ">

        <!-- Badge Profesional -->
        <div lass="flex justify-center items-center p-4 mx-2 bg-gray-50 border border-gray-200 rounded-2xl shadow-md">
    <p class="font-bold font-madani text-2xl">Profesional
    </p>
        </div>
        <h2 class="text-3xl py-4 text-center font-madani font-bold">Editar Perfil</h2>

        <?php
            session_start();
            if (isset($_SESSION['error'])) {
                echo '<div class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-100 font-bold text-center">'
                    . $_SESSION['error'] . '</div>';
                unset($_SESSION['error']);
            }
            if (isset($_SESSION['success'])) {
                echo '<div class="p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-100 font-bold text-center">'
                    . $_SESSION['success'] . '</div>';
                unset($_SESSION['success']);
            }
        ?>
        <form action="/Controllers/EditarProfesionalDao.php" 
            method="POST" 
            enctype="multipart/form-data" 
            class="space-y-4 xl:space-y-0 xl:grid xl:grid-cols-2 xl:gap-x-8 xl:gap-y-4">

            <!-- Foto de Perfil -->
            <div class="col-span-2 flex justify-center bg-gray-50 border border-gray-200 rounded-2xl shadow-md p-8 ">
                <label for="fotoPerfil" class="cursor-pointer group flex flex-col items-center w-full">
                    <p class="text-xl text-gray-800 group-hover:text-gray-500 text-center my-4">
                        Haz clic para subir tu foto de perfil
                    </p>
                    <div class="w-32 h-32 rounded-full bg-gray-200 flex items-center justify-center overflow-hidden mx-auto">
                        <img id="avatarPreview" src="/Views/assets/img/iconos/Gemini_Generated_Image_raoxjfraoxjfraox.svg"
                            alt="avatar" class="w-20 h-20 object-contain pointer-events-none">
                    </div>
                    <input type="file" id="fotoPerfil" name="fotoPerfil" accept="image/*" class="hidden">
                </label>
            </div>

            <!-- Nombres -->
            <div>
                <label for="nombres" class="text-xl font-bold mb-2">Nombres</label>
                    <input type="text" name="nombres" id="nombres" placeholder="Ej: Laura Sofia" required
                        class="w-full border border-gray-400 rounded p-2">
            </div>
        
            <!-- Apellidos -->
            <div>
                <label for="apellidos" class="text-xl font-bold mb-2">Apellidos</label>
                <input type="text" name="apellidos" id="apellidos" placeholder="Ej: Martínez Gomez" required
                        class="w-full border border-gray-400 rounded p-2">
            </div>

            <!-- Servicio -->
            <div>
                <label for="servicio" class="text-xl font-bold mb-2">Servicio que ofrece</label>
                <select id="servicio" name="servicio" required class="w-full border rounded p-2">
                    <option value="" disabled selected>Selecciona un servicio</option>
                    <option>Plomería</option>
                    <option>Electricidad</option>
                    <option>Carpintería</option>
                    <option>Pintura</option>
                    <option>Otros</option>
                </select>

                <select id="servicioAdicional" name="servicioAdicional" class="w-full border rounded p-2 mt-2">
                    <option value="Ninguno" selected>No ninguno</option>
                    <option>Plomería</option>
                    <option>Electricidad</option>
                    <option>Carpintería</option>
                    <option>Pintura</option>
                    <option>Otros</option>
                </select>
            </div>

            <!-- Experiencia -->
            <div>
                <label for="experiencia" class="text-xl font-bold mb-2">Experiencia</label>
                <textarea id="experiencia" name="experiencia" rows="3"
                    placeholder="Ej: 3 años trabajando en mantenimiento de hogares..." required
                    class="w-full border rounded p-2"></textarea>
            </div>

            <!-- Fecha de Nacimiento -->
            <div>
                <label for="fechaNacimiento" class="text-xl font-bold mb-2">Fecha de Nacimiento</label>
                <input type="date" name="fechaNacimiento" id="fechaNacimiento" required
                        class="w-full border border-gray-400 rounded p-2">
            </div>

            <!-- Tipo de Documento -->
            <div>
                <label for="tipoDocumento" class="text-xl font-bold mb-2">Tipo de Documento</label>
                <select name="tipoDocumento" id="tipoDocumento" required class="w-full border rounded p-2">
                    <option value="" disabled selected>Seleccione un tipo de documento</option>
                    <option value="cedula">Cédula</option>
                    <option value="pasaporte">Pasaporte</option>
                    <option value="cedulaExtranjera">Cédula Extranjera</option>
                </select>
            </div>

            <!-- Número de Documento -->
            <div>
                <label for="documento" class="text-xl font-bold mb-2">Número de Documento</label>
                <input type="number" name="documento" id="documento" placeholder="Ej: 1234567890"
                        min="1" max="9999999999" required class="w-full border border-gray-400 rounded p-2">
            </div>

            <!-- Teléfono -->
            <div>
                <label for="telefono" class="text-xl font-bold mb-2">Teléfono</label>
                <input type="number" name="telefono" id="telefono" placeholder="Ej: 1234567890"
                        min="1" max="9999999999" required class="w-full border border-gray-400 rounded p-2">
            </div>

            <!-- Correo Electrónico -->
            <div>
                <label for="email" class="text-xl font-bold mb-2">Correo electrónico</label>
                <input type="email" name="email" id="email" placeholder="correo@ejemplo.com" required
                        class="w-full border border-gray-400 rounded p-2">
            </div>

            <!-- Contraseña -->
            <div>
                <label for="contrasena" class="text-xl font-bold mb-2">Contraseña</label>
                <input type="password" name="contrasena" id="contrasena" placeholder="Ingrese su contraseña"
                        required minlength="6" maxlength="25" class="w-full border border-gray-400 rounded p-2">
            </div>

            <!-- Dirección -->
            <div>
                <label for="direccion" class="text-xl font-bold mb-2">Dirección</label>
                <textarea name="direccion" id="direccion" rows="2"
                        placeholder="Carrera 8 # 45 - 20, Bogotá..." required
                        class="w-full border border-gray-400 rounded p-2"></textarea>
            </div>

            <!-- Confirmar Contraseña -->
            <div>
                <label for="confirmarContrasena" class="text-xl font-bold mb-2">Confirmar Contraseña</label>
                <input type="password" name="confirmarContrasena" id="confirmarContrasena"
                        placeholder="Repita su contraseña" required minlength="6" maxlength="25"
                        class="w-full border border-gray-400 rounded p-2">
            </div>

            <!-- Guardar Cambios -->
            <div class="col-span-2">
                <button type="submit"
                    class="w-full bg-[#b8a57b] rounded-2xl shadow-md text-white p-3 hover:bg-[#1f1f1f] font-bold">
                    Guardar Cambios
                </button>
            </div>
        </form>
    </section>
</body>

</html>

<script>
    function redirigirProfesional(e) 
    {
        e.preventDefault();
        window.location.href = "/Views/modulo-usuarios/HomePlusFull/index.php";
    }

    const inputFile = document.getElementById('fotoPerfil');
    const avatarPreview = document.getElementById('avatarPreview');

    inputFile.addEventListener('change', function (event) 
    {
        const file = event.target.files[0];
        if (file) 
        {
            const reader = new FileReader();
            reader.onload = function (e) 
            {
                avatarPreview.src = e.target.result;
                avatarPreview.classList.add("object-cover");
                avatarPreview.classList.remove("object-contain");
            };
            reader.readAsDataURL(file);
        }
    });
</script>
</body>

</html>
