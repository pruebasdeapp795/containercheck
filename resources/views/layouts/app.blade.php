<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Inspecciones</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>

<body class="bg-gray-100 font-sans leading-normal tracking-normal">

    <nav class=" text-white p-4 shadow-lg" style="background-color: #002c73;">
        <div class="container mx-auto flex justify-between items-center">
            <a href="/" class="text-xl font-bold ms-4"> <i class="fa-solid fa-truck"></i> ContainerCheck</a>

            <div class="flex items-center space-x-4 me-3">
                @auth
                    @if(auth()->user()->role === 'admin')
                        <a href="{{ route('admin.users.index') }}" class="hover:text-gray-300">Usuarios</a>
                        <a href="{{ route('admin.phases.index') }}" class="hover:text-gray-300">Fases y Campos</a>
                    @elseif(auth()->user()->role === 'inspector')
                        <a href="{{ route('inspector.inspections.index') }}" class="hover:text-gray-300">Mis Inspecciones</a>
                    @endif

                    <form action="{{ route('logout') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit"
                            class="bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-3 me-4 rounded text-sm">
                            Salir
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="hover:text-gray-300">Ingresar</a>
                @endauth
            </div>
        </div>
    </nav>

    <div class="container mx-auto p-6">
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        @if($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </div>

</body>

</html>