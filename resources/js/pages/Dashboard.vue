<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import {
    ShieldCheck,
    LogOut,
    MousePointerClick,
} from 'lucide-vue-next';
</script>

<template>
    <Head title="Inicio | Grupo Gilant" />

    <div
        class="flex h-full flex-1 flex-col gap-5 overflow-x-auto bg-[#F7F4EC] p-4 sm:p-6 lg:px-8"
    >
        <!-- Bienvenida principal -->
        <section
            class="relative overflow-hidden rounded-2xl border border-[#0B2B20] bg-gradient-to-br from-[#123D2D] to-[#0B2B20] p-6 sm:p-10"
        >
            <!-- Elementos decorativos -->
            <div
                class="pointer-events-none absolute -right-16 -top-20 h-64 w-64 rounded-full border border-[#C49A5A]/25"
            />

            <div
                class="pointer-events-none absolute -bottom-24 right-36 h-48 w-48 rounded-full border border-[#C49A5A]/15"
            />

            <div class="relative z-10 max-w-3xl">
                <p
                    class="mt-2 text-sm font-semibold uppercase tracking-[0.16em] text-[#C49A5A]"
                >
                    Grupo Gilant
                </p>

                <h1
                    class="mt-3 text-3xl font-semibold tracking-tight text-[#F7F4EC]! sm:text-4xl"
                >
                    Bienvenido al panel administrativo
                </h1>

                <div class="mt-6 h-[3px] w-14 rounded-full bg-[#C49A5A]" />
            </div>
        </section>

        <!-- Guía de uso -->
        <section class="space-y-4">
            <div>
                <h2 class="text-lg font-semibold text-[#123D2D]">
                    Guía de uso
                </h2>

                <p class="mt-1 text-sm leading-6 text-[#747B73]">
                    Consulta estas indicaciones para utilizar el sistema
                    correctamente.
                </p>
            </div>

            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <!-- Navegación -->
                <article
                    class="rounded-2xl border border-[#E6DFCF] bg-white p-5 sm:p-6"
                >
                    <div
                        class="flex h-11 w-11 items-center justify-center rounded-xl bg-[#F3ECDD] text-[#A98145]"
                    >
                        <MousePointerClick class="h-5 w-5" />
                    </div>

                    <h3 class="mt-4 text-base font-semibold text-[#123D2D]">
                        Navegación del sistema
                    </h3>

                    <p class="mt-2 text-sm leading-6 text-[#747B73]">
                        Utiliza el menú lateral para acceder a los diferentes
                        módulos del sistema. Selecciona la sección que
                        necesitas y consulta las opciones disponibles según
                        los permisos de tu cuenta.
                    </p>
                </article>

                <!-- Cerrar sesión -->
                <article
                    class="rounded-2xl border border-[#E6DFCF] bg-white p-5 sm:p-6"
                >
                    <div
                        class="flex h-11 w-11 items-center justify-center rounded-xl bg-[#F5E8E2] text-[#A4523A]"
                    >
                        <LogOut class="h-5 w-5" />
                    </div>

                    <h3 class="mt-4 text-base font-semibold text-[#123D2D]">
                        Cómo cerrar sesión
                    </h3>

                    <p class="mt-2 text-sm leading-6 text-[#747B73]">
                        Cuando termines tus actividades, sigue estos pasos
                        para salir de tu cuenta:
                    </p>

                    <ol class="mt-4 space-y-3">
                        <li class="flex items-start gap-3">
                            <span
                                class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-[#123D2D] text-xs font-semibold text-[#F7F4EC]"
                            >
                                1
                            </span>

                            <p class="text-sm leading-6 text-[#747B73]">
                                Dirígete a la parte inferior del menú lateral.
                            </p>
                        </li>

                        <li class="flex items-start gap-3">
                            <span
                                class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-[#123D2D] text-xs font-semibold text-[#F7F4EC]"
                            >
                                2
                            </span>

                            <p class="text-sm leading-6 text-[#747B73]">
                                Localiza tu nombre o abre las opciones de tu
                                cuenta.
                            </p>
                        </li>

                        <li class="flex items-start gap-3">
                            <span
                                class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-[#123D2D] text-xs font-semibold text-[#F7F4EC]"
                            >
                                3
                            </span>

                            <p class="text-sm leading-6 text-[#747B73]">
                                Selecciona
                                <strong class="font-semibold text-[#123D2D]">
                                    Cerrar sesión
                                </strong>
                                para finalizar tu acceso al sistema.
                            </p>
                        </li>
                    </ol>
                </article>
            </div>
        </section>

        <!-- Seguridad -->
        <section
            class="flex items-start gap-4 rounded-2xl border border-[#E6DFCF] bg-white p-5 sm:p-6"
        >
            <div
                class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-[#E9EDE4] text-[#71845B]"
            >
                <ShieldCheck class="h-5 w-5" />
            </div>

            <div>
                <h2 class="text-sm font-semibold text-[#123D2D]">
                    Seguridad de tu cuenta
                </h2>

                <p class="mt-1 text-sm leading-6 text-[#747B73]">
                    Protege tus credenciales y evita compartirlas con otras
                    personas. Si utilizas un equipo compartido, cierra tu
                    sesión antes de retirarte para proteger la información
                    de Grupo Gilant.
                </p>
            </div>
        </section>
    </div>
</template>
