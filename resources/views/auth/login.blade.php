<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistema de Inspecciones</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100">

    <div class="flex h-screen w-full">

        <div class="hidden md:flex md:w-1/2 lg:w-2/3 bg-auto bg-no-repeat bg-center"
            style="background-image: url('{{ asset('imagenes/imgprovcontainer.png') }}');">
            <div class="w-full h-full bg-black bg-opacity-30 flex items-center justify-center">
                <h1 class="text-white text-4xl font-bold px-10 text-center">Sistema de Inspecciones</h1>
            </div>
        </div>

        <div class="w-full md:w-1/2 lg:w-1/3 flex items-center justify-center bg-white p-8">
            <div class="w-full max-w-md">

                <h2 class="text-3xl font-bold mb-6" style="color:#002c73;">Iniciar Sesión</h2>
                <p class="text-gray-500 mb-8">Bienvenido, por favor ingresa tus datos.</p>

                @if ($errors->any())
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                        <ul class="text-sm">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('login') }}" method="POST">
                    @csrf

                    <div class="mb-4">
                        <label for="email" class="block  text-sm font-semibold mb-2" style="color:#002c73;">Email</label>
                        <input type="email" name="email" id="email"
                            class="appearance-none border border-gray-300 rounded-lg w-full py-3 px-4 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                            placeholder="correo@ejemplo.com" required autofocus>
                    </div>

                    <div class="mb-6">
                        <label for="password" class="block  text-sm font-semibold mb-2" style="color:#002c73;">Contraseña</label>
                        <input type="password" name="password" id="password"
                            class="appearance-none border border-gray-300 rounded-lg w-full py-3 px-4 text-gray-700 mb-3 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                            placeholder="********" required>
                    </div>

                    <div class="flex items-center justify-between">
                        <button type="submit"
                            class="hover:bg-blue-700 text-white font-bold py-3 px-4 rounded-lg transition duration-200 w-full shadow-lg" style="background-color: #002c73">
                            Ingresar
                        </button>
                    </div>
                </form>

                <div class="mt-8 text-center">
                    <a href="#" class="text-sm text-blue-600 hover:underline font-medium">¿Eres participante? Ingresa
                        aquí</a>
                </div>
            </div>
        </div>
    </div>

</body>

</html>