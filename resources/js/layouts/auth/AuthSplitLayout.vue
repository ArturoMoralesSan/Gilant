<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { home } from '@/routes';

defineProps<{
    title?: string;
    description?: string;
}>();
</script>

<template>
    <div class="relative grid min-h-svh bg-[#F7F4EC] lg:grid-cols-2">
        <!-- PANEL VISUAL -->
        <aside
            class="relative hidden min-h-svh flex-col justify-between overflow-hidden bg-[#123D2D] p-10 text-white lg:flex xl:p-14"
        >
            <!-- Imagen de fondo -->
            <div
                class="absolute inset-0 bg-cover bg-center"
                style="background-image: url('/images/login.jpeg')"
            />

            <!-- Capa verde Gilant sobre la fotografía -->
            <div
                class="absolute inset-0 bg-gradient-to-b from-[#0B2B20]/85 via-[#123D2D]/60 to-[#0B2B20]/95"
            />

            <!-- Decoración -->
            <div
                class="pointer-events-none absolute -right-36 -top-32 size-[440px] rounded-full border border-[#C49A5A]/[0.18]"
            />

            <div
                class="pointer-events-none absolute -right-20 -top-16 size-[300px] rounded-full border border-[#C49A5A]/[0.12]"
            />

            <!-- Logotipo Gilant -->
            <Link
                :href="home()"
                class="relative z-10 inline-flex w-fit items-center"
                aria-label="Grupo Gilant, ir al inicio"
            >
                <span
                    class="text-4xl font-semibold uppercase tracking-[0.22em] text-[#F7F4EC]"
                >
                    Gilant
                </span>
            </Link>

            <!-- Mensaje principal -->
            <div class="relative z-10 max-w-xl py-12">
                <div
                    class="mb-7 inline-flex items-center gap-2.5 rounded-full border border-[#C49A5A]/30 bg-white/[0.06] px-4 py-2"
                >
                    <span class="size-2 rounded-full bg-[#C49A5A]" />

                    <span
                        class="text-xs font-medium tracking-[0.12em] text-[#F7F4EC]"
                    >
                        GRUPO GILANT DEL NOROESTE, S.A. DE C.V.
                    </span>
                </div>

                <h2
                    class="max-w-lg text-4xl font-medium leading-[1.15] tracking-tight xl:text-5xl"
                >
                    Construcción en madera estructural
                    <span class="block text-[#C49A5A]">
                        y obras exteriores.
                    </span>
                </h2>

                <div class="mt-9 h-[3px] w-14 rounded-full bg-[#C49A5A]" />

                <p
                    class="mt-6 text-sm font-medium tracking-[0.25em] text-[#F7F4EC]/80"
                >
                    NATURALEZA &nbsp;·&nbsp; DISEÑO &nbsp;·&nbsp; DURABILIDAD
                </p>
            </div>

            <!-- Pie del panel -->
            <div
                class="relative z-10 flex items-center justify-between gap-4 border-t border-[#F7F4EC]/15 pt-5 text-xs text-[#F7F4EC]/60"
            >
                <span>Grupo Gilant del Noroeste</span>
                <span>Acceso seguro a tu cuenta</span>
            </div>
        </aside>

        <!-- PANEL DEL FORMULARIO -->
        <main
            class="flex min-h-svh flex-col items-center justify-center px-5 py-10 sm:px-8 lg:px-10 xl:px-14"
        >
            <div class="w-full max-w-[440px]">
                <!-- Logotipo en móvil -->
                <Link
                    :href="home()"
                    class="mb-8 flex flex-col items-center justify-center gap-1 lg:hidden"
                    aria-label="Grupo Gilant, ir al inicio"
                >
                    <span
                        class="text-3xl font-semibold uppercase tracking-[0.22em] text-[#123D2D]"
                    >
                        Gilant
                    </span>

                    <span
                        class="text-[10px] font-medium tracking-[0.25em] text-[#71845B]"
                    >
                        NATURALEZA · DISEÑO · DURABILIDAD
                    </span>
                </Link>

                <!-- CARD DEL FORMULARIO -->
                <section
                    class="overflow-hidden rounded-2xl border border-[#123D2D]/[0.12] bg-white shadow-[0_12px_45px_rgba(11,43,32,0.08)]"
                >
                    <!-- Encabezado -->
                    <div
                        class="border-b border-[#123D2D]/[0.08] px-6 pb-6 pt-8 sm:px-9 sm:pt-9"
                    >
                        <h1
                            v-if="title"
                            class="text-2xl font-semibold tracking-tight text-[#123D2D] sm:text-[28px]"
                        >
                            {{ title }}
                        </h1>

                        <p
                            v-if="description"
                            class="mt-2 text-sm leading-6 text-[#747B73]"
                        >
                            {{ description }}
                        </p>
                    </div>

                    <!-- Formulario original -->
                    <div class="auth-form px-6 py-7 sm:px-9 sm:py-8">
                        <slot />
                    </div>
                </section>

                <!-- Pie -->
                <p
                    class="mt-7 px-3 text-center text-xs leading-5 text-[#747B73]"
                >
                    © {{ new Date().getFullYear() }} Grupo Gilant del Noroeste,
                    S.A. de C.V. Todos los derechos reservados.
                </p>
            </div>
        </main>
    </div>
</template>

<style>
/* Botones con colores Gilant dentro del formulario */
.auth-form [type='submit'] {
    background-color: #123d2d;
    border-color: #123d2d;
    color: #ffffff;
    transition:
        background-color 180ms ease,
        border-color 180ms ease;
}

.auth-form [type='submit']:hover {
    background-color: #0b2b20;
    border-color: #0b2b20;
}

.auth-form [type='submit']:focus-visible {
    outline: 2px solid #c49a5a;
    outline-offset: 3px;
}

/* Títulos del formulario */
.auth-form h1,
.auth-form h2,
.auth-form h3 {
    color: #123d2d;
}

/* Labels de los campos */
.auth-form label {
    color: #123d2d;
}
</style>
