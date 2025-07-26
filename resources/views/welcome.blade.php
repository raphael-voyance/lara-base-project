@extends('layouts.app')

@section('content')
    <div class="relative min-h-screen bg-gray-100 bg-center flex flex-col justify-center items-center">
        @if (Route::has('login'))
            <div class="p-6 text-right w-full max-w-7xl mx-auto">
                @auth
                    <a href="{{ route('home') }}" class="font-semibold text-gray-600 hover:text-gray-900 focus:outline focus:outline-2 focus:rounded-sm focus:outline-indigo-500">Accueil</a>
                @else
                    <a href="{{ route('login') }}" class="font-semibold text-gray-600 hover:text-gray-900 focus:outline focus:outline-2 focus:rounded-sm focus:outline-indigo-500">Connexion</a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="ml-4 font-semibold text-gray-600 hover:text-gray-900 focus:outline focus:outline-2 focus:rounded-sm focus:outline-indigo-500">Inscription</a>
                    @endif
                @endauth
            </div>
        @endif

        <div class="p-6 mx-auto max-w-4xl w-full">
            <div class="flex flex-col items-center mb-12">
                <svg class="w-auto h-20 text-indigo-600 bg-gray-100 mb-6" viewBox="0 0 62 65" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M61.8548 14.6253C61.8778 14.7102 61.8895 14.7978 61.8897 14.8858V28.5615C61.8898 28.737 61.8434 28.9095 61.7554 29.0614C61.6675 29.2132 61.5409 29.3392 61.3887 29.4265L49.9104 36.0351V49.1337C49.9104 49.4902 49.7209 49.8192 49.4118 49.9987L25.4519 63.7916C25.3971 63.8227 25.3372 63.8427 25.2774 63.8639C25.255 63.8714 25.2338 63.8851 25.2101 63.8913C25.0426 63.9354 24.8666 63.9354 24.6991 63.8913C24.6716 63.8838 24.6467 63.8689 24.6205 63.8589C24.5657 63.8389 24.5084 63.8215 24.456 63.7916L0.501061 49.9987C0.348882 49.9113 0.222437 49.7853 0.134469 49.6334C0.0465019 49.4816 0.000120578 49.3092 0 49.1337L0 8.10652C0 8.01678 0.0124642 7.92953 0.0348998 7.84477C0.0423783 7.8161 0.0598282 7.78993 0.0697995 7.76126C0.0884958 7.70891 0.105946 7.65531 0.133367 7.6067C0.152063 7.5743 0.179485 7.54812 0.20192 7.51821C0.230588 7.47832 0.256763 7.43719 0.290416 7.40229C0.319084 7.37362 0.356476 7.35243 0.388883 7.32751C0.425029 7.29759 0.457436 7.26518 0.498568 7.2415L12.4779 0.345059C12.6296 0.257786 12.8015 0.211853 12.9765 0.211853C13.1515 0.211853 13.3234 0.257786 13.475 0.345059L25.4531 7.2415H25.4556C25.4955 7.26643 25.5292 7.29759 25.5653 7.32626C25.5977 7.35119 25.6339 7.37362 25.6625 7.40104C25.6974 7.43719 25.7224 7.47832 25.7523 7.51821C25.7735 7.54812 25.8021 7.5743 25.8196 7.6067C25.8483 7.65656 25.8645 7.70891 25.8844 7.76126C25.8944 7.78993 25.9118 7.8161 25.9193 7.84602C25.9423 7.93096 25.954 8.01853 25.9542 8.10652V33.7317L35.9355 27.9844V14.8846C35.9355 14.7973 35.948 14.7088 35.9704 14.6253C35.9792 14.5954 35.9954 14.5692 36.0053 14.5405C36.0253 14.4882 36.0427 14.4346 36.0702 14.386C36.0888 14.3536 36.1163 14.3274 36.1375 14.2975C36.1674 14.2576 36.1923 14.2165 36.2272 14.1816C36.2559 14.1529 36.292 14.1317 36.3244 14.1068C36.3618 14.0769 36.3942 14.0445 36.4341 14.0208L48.4147 7.12434C48.5663 7.03694 48.7383 6.99094 48.9133 6.99094C49.0883 6.99094 49.2602 7.03694 49.4118 7.12434L61.3899 14.0208C61.4323 14.0457 61.4647 14.0769 61.5021 14.1055C61.5333 14.1305 61.5694 14.1529 61.5981 14.1803C61.633 14.2165 61.6579 14.2576 61.6878 14.2975C61.7103 14.3274 61.7377 14.3536 61.7551 14.386C61.7838 14.4346 61.8 14.4882 61.8199 14.5405C61.8312 14.5692 61.8474 14.5954 61.8548 14.6253ZM59.893 27.9844V16.6121L55.7013 19.0252L49.9104 22.3593V33.7317L59.8942 27.9844H59.893ZM47.9149 48.5566V37.1768L42.2187 40.4299L25.953 49.7133V61.2003L47.9149 48.5566ZM1.99677 9.83281V48.5566L23.9562 61.199V49.7145L12.4841 43.2219L12.4804 43.2194L12.4754 43.2169C12.4368 43.1945 12.4044 43.1621 12.3682 43.1347C12.3371 43.1097 12.3009 43.0898 12.2735 43.0624L12.271 43.0586C12.2386 43.0275 12.2162 42.9888 12.1887 42.9539C12.1638 42.9203 12.1339 42.8916 12.114 42.8567L12.1127 42.853C12.0903 42.8156 12.0766 42.7707 12.0604 42.7283C12.0442 42.6909 12.023 42.656 12.013 42.6161C12.0005 42.5688 11.998 42.5177 11.9931 42.4691C11.9881 42.4317 11.9781 42.3943 11.9781 42.3569V15.5801L6.18848 12.2446L1.99677 9.83281ZM12.9777 2.36177L2.99764 8.10652L12.9752 13.8513L22.9541 8.10527L12.9752 2.36177H12.9777ZM18.1678 38.2138L23.9574 34.8809V9.83281L19.7657 12.2459L13.9749 15.5801V40.6281L18.1678 38.2138ZM48.9133 9.14105L38.9344 14.8858L48.9133 20.6305L58.8909 14.8846L48.9133 9.14105ZM47.9149 22.3593L42.124 19.0252L37.9323 16.6121V27.9844L43.7219 31.3174L47.9149 33.7317V22.3593ZM24.9533 47.987L39.59 39.631L46.9065 35.4555L36.9352 29.7145L25.4544 36.3242L14.9907 42.3482L24.9533 47.987Z" fill="currentColor"/>
                </svg>
                <h1 class="text-4xl font-bold text-center text-gray-900 mb-3">Base Project Laravel</h1>
                <p class="text-xl text-indigo-700 font-semibold mb-6 text-center">Un point de départ moderne et complet pour vos applications web</p>
                <div class="flex flex-wrap gap-4 justify-center">
                    <a href="{{ route('home') }}" class="inline-block px-8 py-3 bg-indigo-600 text-white rounded-lg shadow-lg hover:bg-indigo-700 transition duration-300 font-semibold">Commencer</a>
                    @auth
                        <a href="{{ route('doc.plugins') }}" class="inline-block px-8 py-3 bg-emerald-600 text-white rounded-lg shadow-lg hover:bg-emerald-700 transition duration-300 font-semibold">Documentation plugins</a>
                    @endif
                </div>
            </div>

            <div class="mb-12">
                <h2 class="text-3xl font-semibold text-gray-800 mb-4 text-center">Fonctionnalités principales</h2>
                <p class="text-gray-600 mb-6 text-center max-w-3xl mx-auto">Ce projet Laravel 12 est enrichi de nombreux packages pour accélérer le développement d'applications modernes, multilingues et robustes.</p>
            </div>

            <div class="grid grid-cols-1 gap-8 md:grid-cols-2 lg:grid-cols-3 mb-12">
                <!-- Authentification -->
                <div class="p-6 bg-white rounded-xl shadow-lg hover:shadow-xl transition duration-300 flex flex-col items-center text-center">
                    <div class="w-12 h-12 bg-indigo-100 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </div>
                    <h3 class="font-semibold text-gray-800 mb-2">Authentification complète</h3>
                    <p class="text-gray-500 text-sm">Livewire, gestion des emails, vérification, réinitialisation de mot de passe</p>
                </div>

                <!-- Architecture modulaire -->
                <div class="p-6 bg-white rounded-xl shadow-lg hover:shadow-xl transition duration-300 flex flex-col items-center text-center">
                    <div class="w-12 h-12 bg-emerald-100 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                        </svg>
                    </div>
                    <h3 class="font-semibold text-gray-800 mb-2">Architecture modulaire</h3>
                    <p class="text-gray-500 text-sm">Prise en charge modulaire avec une architecture fluide de création de plugins</p>
                </div>

                <!-- Permissions -->
                <div class="p-6 bg-white rounded-xl shadow-lg hover:shadow-xl transition duration-300 flex flex-col items-center text-center">
                    <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                    </div>
                    <h3 class="font-semibold text-gray-800 mb-2">Gestion des permissions</h3>
                    <p class="text-gray-500 text-sm">Système de rôles et permissions granulaire avec Spatie</p>
                </div>

                <!-- Médias -->
                <div class="p-6 bg-white rounded-xl shadow-lg hover:shadow-xl transition duration-300 flex flex-col items-center text-center">
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <h3 class="font-semibold text-gray-800 mb-2">Gestion des médias</h3>
                    <p class="text-gray-500 text-sm">Upload et gestion avancée des fichiers avec Media Library</p>
                </div>

                <!-- SEO -->
                <div class="p-6 bg-white rounded-xl shadow-lg hover:shadow-xl transition duration-300 flex flex-col items-center text-center">
                    <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                    <h3 class="font-semibold text-gray-800 mb-2">SEO optimisé</h3>
                    <p class="text-gray-500 text-sm">Outils SEO, sitemap automatique et métadonnées avancées</p>
                </div>

                <!-- Notifications -->
                <div class="p-6 bg-white rounded-xl shadow-lg hover:shadow-xl transition duration-300 flex flex-col items-center text-center">
                    <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-5 5v-5zM4.19 4.19A2 2 0 004 6v10a2 2 0 002 2h10a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-1.81 1.19z"/>
                        </svg>
                    </div>
                    <h3 class="font-semibold text-gray-800 mb-2">Notifications toast</h3>
                    <p class="text-gray-500 text-sm">Système de notifications élégant et interactif</p>
                </div>
            </div>

            <!-- Stack technique -->
            <div class="mb-12">
                <h2 class="text-2xl font-semibold text-gray-800 mb-6 text-center">Stack technique</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="bg-white rounded-xl shadow-lg p-6">
                        <h3 class="font-semibold text-gray-800 mb-4 flex items-center">
                            <svg class="w-5 h-5 text-indigo-600 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/>
                            </svg>
                            Backend (PHP)
                        </h3>
                        <ul class="space-y-2 text-sm text-gray-600">
                            <li class="flex items-center"><span class="w-2 h-2 bg-indigo-500 rounded-full mr-3"></span>Laravel 12</li>
                            <li class="flex items-center"><span class="w-2 h-2 bg-indigo-500 rounded-full mr-3"></span>Livewire 3.6</li>
                            <li class="flex items-center"><span class="w-2 h-2 bg-indigo-500 rounded-full mr-3"></span>Spatie packages (permissions, media, backup)</li>
                            <li class="flex items-center"><span class="w-2 h-2 bg-indigo-500 rounded-full mr-3"></span>Actions Laravel</li>
                            <li class="flex items-center"><span class="w-2 h-2 bg-indigo-500 rounded-full mr-3"></span>Support multilingue</li>
                        </ul>
                    </div>
                    <div class="bg-white rounded-xl shadow-lg p-6">
                        <h3 class="font-semibold text-gray-800 mb-4 flex items-center">
                            <svg class="w-5 h-5 text-emerald-600 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zM21 5a2 2 0 00-2-2h-4a2 2 0 00-2 2v12a4 4 0 004 4h4a2 2 0 002-2V5z"/>
                            </svg>
                            Frontend (JS/CSS)
                        </h3>
                        <ul class="space-y-2 text-sm text-gray-600">
                            <li class="flex items-center"><span class="w-2 h-2 bg-emerald-500 rounded-full mr-3"></span>Vite 6.2.4</li>
                            <li class="flex items-center"><span class="w-2 h-2 bg-emerald-500 rounded-full mr-3"></span>Tailwind CSS 4.0</li>
                            <li class="flex items-center"><span class="w-2 h-2 bg-emerald-500 rounded-full mr-3"></span>Alpine.js</li>
                            <li class="flex items-center"><span class="w-2 h-2 bg-emerald-500 rounded-full mr-3"></span>Composants Livewire</li>
                            <li class="flex items-center"><span class="w-2 h-2 bg-emerald-500 rounded-full mr-3"></span>Interface responsive</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Documentation -->
            <div class="mb-12">
                <h2 class="text-2xl font-semibold text-gray-800 mb-6 text-center">Documentation et ressources</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <a href="https://laravel.com/docs" target="_blank" class="group p-6 bg-white rounded-xl shadow-lg hover:shadow-xl transition duration-300 flex flex-col items-center text-center">
                        <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center mb-4 group-hover:bg-red-200 transition duration-300">
                            <svg class="w-6 h-6 text-red-600" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/>
                            </svg>
                        </div>
                        <h3 class="font-semibold text-gray-800 mb-2 group-hover:text-red-600 transition duration-300">Laravel</h3>
                        <p class="text-gray-500 text-sm">Documentation officielle du framework</p>
                    </a>
                    <a href="https://tailwindcss.com/docs" target="_blank" class="group p-6 bg-white rounded-xl shadow-lg hover:shadow-xl transition duration-300 flex flex-col items-center text-center">
                        <div class="w-12 h-12 bg-cyan-100 rounded-lg flex items-center justify-center mb-4 group-hover:bg-cyan-200 transition duration-300">
                            <svg class="w-6 h-6 text-cyan-600" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                            </svg>
                        </div>
                        <h3 class="font-semibold text-gray-800 mb-2 group-hover:text-cyan-600 transition duration-300">Tailwind CSS</h3>
                        <p class="text-gray-500 text-sm">Framework CSS utilitaire</p>
                    </a>
                    <a href="https://livewire.laravel.com/docs" target="_blank" class="group p-6 bg-white rounded-xl shadow-lg hover:shadow-xl transition duration-300 flex flex-col items-center text-center">
                        <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center mb-4 group-hover:bg-purple-200 transition duration-300">
                            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                            </svg>
                        </div>
                        <h3 class="font-semibold text-gray-800 mb-2 group-hover:text-purple-600 transition duration-300">Livewire</h3>
                        <p class="text-gray-500 text-sm">Composants dynamiques en PHP</p>
                    </a>
                    <a href="https://filamentphp.com/docs" target="_blank" class="group p-6 bg-white rounded-xl shadow-lg hover:shadow-xl transition duration-300 flex flex-col items-center text-center">
                        <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mb-4 group-hover:bg-blue-200 transition duration-300">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                            </svg>
                        </div>
                        <h3 class="font-semibold text-gray-800 mb-2 group-hover:text-blue-600 transition duration-300">Filament</h3>
                        <p class="text-gray-500 text-sm">Interface d'administration moderne</p>
                    </a>
                    <a href="https://spatie.be/open-source" target="_blank" class="group p-6 bg-white rounded-xl shadow-lg hover:shadow-xl transition duration-300 flex flex-col items-center text-center">
                        <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center mb-4 group-hover:bg-green-200 transition duration-300">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
                            </svg>
                        </div>
                        <h3 class="font-semibold text-gray-800 mb-2 group-hover:text-green-600 transition duration-300">Spatie</h3>
                        <p class="text-gray-500 text-sm">Packages Laravel de qualité</p>
                    </a>
                                         <a href="https://alpinejs.dev/docs" target="_blank" class="group p-6 bg-white rounded-xl shadow-lg hover:shadow-xl transition duration-300 flex flex-col items-center text-center">
                         <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center mb-4 group-hover:bg-orange-200 transition duration-300">
                             <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                 <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                             </svg>
                         </div>
                         <h3 class="font-semibold text-gray-800 mb-2 group-hover:text-orange-600 transition duration-300">Alpine.js</h3>
                         <p class="text-gray-500 text-sm">Framework JavaScript léger</p>
                     </a>
                     <a href="{{ route('doc.plugins') }}" class="group p-6 bg-white rounded-xl shadow-lg hover:shadow-xl transition duration-300 flex flex-col items-center text-center">
                         <div class="w-12 h-12 bg-emerald-100 rounded-lg flex items-center justify-center mb-4 group-hover:bg-emerald-200 transition duration-300">
                             <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                 <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                             </svg>
                         </div>
                         <h3 class="font-semibold text-gray-800 mb-2 group-hover:text-emerald-600 transition duration-300">Architecture modulaire</h3>
                         <p class="text-gray-500 text-sm">Documentation pour créer vos propres plugins</p>
                     </a>
                </div>
            </div>

            <!-- Call to action -->
            <div class="text-center mb-12">
                <div class="bg-gradient-to-r from-indigo-600 to-purple-600 rounded-2xl p-8 text-white">
                    <h2 class="text-2xl font-bold mb-4">Prêt à commencer ?</h2>
                    <p class="text-indigo-100 mb-6 max-w-2xl mx-auto">Ce projet est conçu pour accélérer le développement de vos applications web. Explorez, modifiez et adaptez ce socle à vos projets !</p>
                                         <div class="flex flex-wrap gap-4 justify-center">
                         <a href="{{ route('home') }}" class="inline-block px-8 py-3 bg-white text-indigo-600 rounded-lg font-semibold hover:bg-gray-100 transition duration-300">Explorer le projet</a>
                         @auth
                             <a href="{{ route('doc.plugins') }}" class="inline-block px-8 py-3 bg-emerald-500 text-white rounded-lg font-semibold hover:bg-emerald-600 transition duration-300">Documentation plugins</a>
                         @else
                             <a href="{{ route('login') }}" class="inline-block px-8 py-3 bg-emerald-500 text-white rounded-lg font-semibold hover:bg-emerald-600 transition duration-300">Se connecter</a>
                         @endif
                     </div>
                </div>
            </div>
        </div>

        <footer class="w-full text-center text-gray-400 text-sm py-8 border-t mt-12">
            <div class="mb-3">
                <span class="font-semibold text-gray-600">Laravel v{{ Illuminate\Foundation\Application::VERSION }}</span>
                <span class="mx-2">•</span>
                <span class="text-gray-500">PHP v{{ PHP_VERSION }}</span>
            </div>
            <div class="mb-3">
                <a href="https://github.com/ton-repo" class="text-indigo-500 hover:text-indigo-600 transition duration-300 mx-2" target="_blank">GitHub</a>
                <span class="text-gray-400">•</span>
                <a href="mailto:contact@tonprojet.fr" class="text-indigo-500 hover:text-indigo-600 transition duration-300 mx-2">Contact</a>
            </div>
            <div class="text-gray-500">&copy; {{ date('Y') }} Raphaël Henry-Navarro. Tous droits réservés.</div>
        </footer>
    </div>
@endsection
