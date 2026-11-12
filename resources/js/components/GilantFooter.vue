
<script setup lang="ts">
import { nextTick, onMounted } from 'vue'

const year = new Date().getFullYear()

const links = [
    { label: 'Inicio', href: '/', page: true },
    { label: 'Nosotros', href: '/nosotros', page: true },
    { label: 'Servicios', href: '#servicios' },
    { label: 'Proyectos', href: '#proyectos' },
    { label: 'Clientes', href: '#clientes' },
    { label: 'Contacto', href: '#contacto' },
]

function navigateToSection(hash: string, isPage = false) {
    if (isPage) {
        window.location.href = hash
        return
    }

    const landingPath = '/'

    if (window.location.pathname !== landingPath) {
        window.location.href = `${landingPath}${hash}`
        return
    }

    const target = document.querySelector(hash)

    if (target) {
        history.pushState(null, '', hash)

        target.scrollIntoView({
            behavior: 'smooth',
            block: 'start',
        })

        return
    }

    window.location.hash = hash
}

async function scrollToInitialHash() {
    if (window.location.pathname !== '/') return

    const hash = window.location.hash

    if (!hash) return

    await nextTick()

    requestAnimationFrame(() => {
        requestAnimationFrame(() => {
            const target = document.querySelector(hash)

            target?.scrollIntoView({
                behavior: 'auto',
                block: 'start',
            })
        })
    })
}

onMounted(() => {
    scrollToInitialHash()
})
</script>

<template>
    <footer class="gilant-footer">
        <div class="gilant-footer-decoration"></div>

        <div class="gilant-container gilant-footer-main">

            <!-- IDENTIDAD -->
            <div class="gilant-footer-brand-column">
                <a
                    class="gilant-brand gilant-brand-light"
                    href="/#inicio"
                    aria-label="Grupo Gilant, inicio"
                    @click.prevent="navigateToSection('#inicio')"
                >
                    <span class="gilant-brand-text">
                        GRUPO GILANT
                    </span>
                </a>

                <p class="gilant-footer-description">
                    Soluciones arquitectónicas y constructivas en madera.
                    Ingeniería, diseño y experiencia para crear espacios
                    que perduran.
                </p>

                <span class="gilant-footer-slogan">
                    CONSTRUCCIÓN EN MADERA ESTRUCTURAL Y OBRAS EXTERIORES
                </span>
            </div>

            <!-- NAVEGACIÓN -->
            <div class="gilant-footer-column">
                <p class="gilant-footer-heading">
                    EXPLORA
                </p>

                <nav
                    class="gilant-footer-navigation"
                    aria-label="Navegación del pie de página"
                >
                    <a
                        v-for="link in links"
                        :key="link.href + link.label"
                        :href="link.page ? link.href : `/${link.href}`"
                        @click.prevent="
                            navigateToSection(link.href, link.page)
                        "
                    >
                        <span>{{ link.label }}</span>
                        <span class="gilant-footer-link-line"></span>
                    </a>
                </nav>
            </div>

            <!-- CONTACTO -->
            <div class="gilant-footer-column gilant-footer-contact">
                <p class="gilant-footer-heading">
                    HABLEMOS DE TU PROYECTO
                </p>

                <a href="tel:+526188357190">
                    <span class="gilant-footer-contact-label">
                        TELÉFONO
                    </span>
                    <span>618 835 7190</span>
                </a>

                <a
                    href="https://wa.me/526181892080"
                    target="_blank"
                    rel="noopener noreferrer"
                >
                    <span class="gilant-footer-contact-label">
                        WHATSAPP
                    </span>
                    <span>618 189 2080</span>
                </a>

                <a href="mailto:info@gilant.com.mx">
                    <span class="gilant-footer-contact-label">
                        CORREO ELECTRÓNICO
                    </span>
                    <span>info@gilant.com.mx</span>
                </a>

                <a
                    class="gilant-footer-contact-cta"
                    href="/#contacto"
                    @click.prevent="navigateToSection('#contacto')"
                >
                    Solicitar cotización
                    <span>↗</span>
                </a>
            </div>

            <!-- VOLVER ARRIBA -->
            <a
                class="gilant-back-top"
                href="/#inicio"
                aria-label="Volver al inicio"
                @click.prevent="navigateToSection('#inicio')"
            >
                <span>↑</span>
                <small>VOLVER ARRIBA</small>
            </a>
        </div>

        <!-- COPYRIGHT -->
        <div class="gilant-footer-bottom">
            <div class="gilant-container gilant-footer-bottom-inner">
                <span>
                    © {{ year }} Grupo Gilant.
                    Todos los derechos reservados.
                </span>

                <span class="gilant-footer-bottom-note">
                    NATURALEZA · DISEÑO · DURABILIDAD
                </span>
            </div>
        </div>
    </footer>
</template>

<style scoped>
/* =========================================================
   FOOTER GILANT
========================================================= */

.gilant-footer {
    position: relative;
    width: 100%;
    overflow: hidden;
    color: #f7f4ec;
    background: #0b2b20;
}

.gilant-footer-decoration {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 3px;
    background: linear-gradient(
        90deg,
        transparent,
        #c49a5a 25%,
        #d8b77e 50%,
        #c49a5a 75%,
        transparent
    );
    opacity: 0.85;
}

.gilant-footer-main {
    position: relative;
    display: grid;
    grid-template-columns: 1.5fr 0.8fr 1.1fr auto;
    align-items: start;
    gap: clamp(2rem, 5vw, 5rem);
    padding-top: 76px;
    padding-bottom: 68px;
}

/* =========================================================
   IDENTIDAD
========================================================= */

.gilant-footer-brand-column {
    max-width: 340px;
}

.gilant-footer-brand-column .gilant-brand {
    display: inline-flex;
    align-items: center;
    text-decoration: none;
}

.gilant-brand-text {
    color: #fffefa;
    font-family: 'Manrope', sans-serif;
    font-size: 21px;
    font-weight: 800;
    letter-spacing: 0.12em;
    white-space: nowrap;
}

.gilant-footer-description {
    max-width: 310px;
    margin: 27px 0 24px;
    color: rgb(247 244 236 / 68%);
    font-family: 'Manrope', sans-serif;
    font-size: 0.88rem;
    font-weight: 400;
    line-height: 1.85;
}

.gilant-footer-slogan {
    display: inline-block;
    color: #c49a5a;
    font-size: 0.65rem;
    font-weight: 700;
    letter-spacing: 0.2em;
}

/* =========================================================
   COLUMNAS
========================================================= */

.gilant-footer-column {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
}

.gilant-footer-heading {
    position: relative;
    margin: 5px 0 27px;
    padding-bottom: 13px;
    color: #d5b47b;
    font-size: 0.68rem;
    font-weight: 700;
    letter-spacing: 0.17em;
}

.gilant-footer-heading::after {
    position: absolute;
    bottom: 0;
    left: 0;
    width: 30px;
    height: 1px;
    background: #c49a5a;
    content: '';
}

/* =========================================================
   NAVEGACIÓN
========================================================= */

.gilant-footer-navigation {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    gap: 13px;
}

.gilant-footer-navigation a {
    display: inline-flex;
    align-items: center;
    gap: 9px;
    color: rgb(247 244 236 / 76%);
    font-size: 0.88rem;
    text-decoration: none;
    transition:
        color 220ms ease,
        transform 220ms ease;
}

.gilant-footer-navigation a:hover {
    color: #d5b47b;
    transform: translateX(4px);
}

.gilant-footer-link-line {
    width: 0;
    height: 1px;
    background: #c49a5a;
    transition: width 220ms ease;
}

.gilant-footer-navigation a:hover .gilant-footer-link-line {
    width: 17px;
}

/* =========================================================
   CONTACTO
========================================================= */

.gilant-footer-contact {
    gap: 19px;
}

.gilant-footer-contact > a:not(.gilant-footer-contact-cta) {
    display: flex;
    flex-direction: column;
    gap: 6px;
    color: #f7f4ec;
    font-size: 0.88rem;
    text-decoration: none;
    transition: color 220ms ease;
}

.gilant-footer-contact > a:not(.gilant-footer-contact-cta):hover {
    color: #d5b47b;
}

.gilant-footer-contact-label {
    color: rgb(247 244 236 / 48%);
    font-size: 0.61rem;
    font-weight: 700;
    letter-spacing: 0.13em;
}

.gilant-footer-contact-cta {
    display: inline-flex;
    align-items: center;
    justify-content: space-between;
    gap: 22px;
    min-width: 190px;
    margin-top: 8px;
    padding: 12px 16px;
    border: 1px solid rgb(196 154 90 / 65%);
    color: #e2c28d;
    font-size: 0.78rem;
    font-weight: 600;
    text-decoration: none;
    transition:
        color 220ms ease,
        background 220ms ease,
        border-color 220ms ease;
}

.gilant-footer-contact-cta span {
    font-size: 1rem;
}

.gilant-footer-contact-cta:hover {
    border-color: #c49a5a;
    color: #0b2b20;
    background: #c49a5a;
}

/* =========================================================
   VOLVER ARRIBA
========================================================= */

.gilant-back-top {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 11px;
    color: rgb(247 244 236 / 68%);
    text-decoration: none;
    transition: color 220ms ease;
}

.gilant-back-top > span {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 43px;
    height: 43px;
    border: 1px solid rgb(196 154 90 / 55%);
    border-radius: 50%;
    color: #d5b47b;
    font-size: 1.3rem;
    transition:
        color 220ms ease,
        background 220ms ease,
        transform 220ms ease;
}

.gilant-back-top small {
    font-size: 0.56rem;
    font-weight: 700;
    letter-spacing: 0.12em;
    white-space: nowrap;
}

.gilant-back-top:hover {
    color: #d5b47b;
}

.gilant-back-top:hover > span {
    color: #0b2b20;
    background: #c49a5a;
    transform: translateY(-4px);
}

/* =========================================================
   COPYRIGHT
========================================================= */

.gilant-footer-bottom {
    position: relative;
    border-top: 1px solid rgb(247 244 236 / 12%);
    background: rgb(0 0 0 / 10%);
}

.gilant-footer-bottom-inner {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 20px;
    min-height: 66px;
    color: rgb(247 244 236 / 55%);
    font-size: 0.72rem;
}

.gilant-footer-bottom-note {
    color: rgb(213 180 123 / 75%);
    font-size: 0.65rem;
    letter-spacing: 0.08em;
}

/* =========================================================
   TABLET
========================================================= */

@media (max-width: 1000px) {
    .gilant-footer-main {
        grid-template-columns: 1.2fr 0.8fr 1fr;
        gap: 3rem 2rem;
    }

    .gilant-back-top {
        grid-column: 1 / -1;
        flex-direction: row;
        justify-self: end;
        margin-top: 5px;
    }

    .gilant-back-top small {
        display: none;
    }
}

/* =========================================================
   MÓVIL
========================================================= */

@media (max-width: 650px) {
    .gilant-footer-main {
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 42px 25px;
        padding-top: 55px;
        padding-bottom: 42px;
    }

    .gilant-footer-brand-column {
        grid-column: 1 / -1;
        max-width: 420px;
    }

    .gilant-brand-text {
        font-size: 19px;
    }

    .gilant-footer-description {
        max-width: 370px;
        margin-top: 20px;
        font-size: 0.83rem;
    }

    .gilant-footer-heading {
        margin-bottom: 21px;
        font-size: 0.62rem;
    }

    .gilant-footer-navigation {
        gap: 12px;
    }

    .gilant-footer-navigation a,
    .gilant-footer-contact > a:not(.gilant-footer-contact-cta) {
        font-size: 0.8rem;
    }

    .gilant-footer-contact {
        gap: 17px;
    }

    .gilant-footer-contact-cta {
        min-width: 0;
        width: 100%;
        gap: 10px;
        padding: 11px;
        font-size: 0.7rem;
    }

    .gilant-back-top {
        grid-column: 1 / -1;
        justify-self: start;
        margin-top: 0;
    }

    .gilant-footer-bottom-inner {
        align-items: flex-start;
        flex-direction: column;
        justify-content: center;
        gap: 7px;
        min-height: 82px;
        padding-top: 14px;
        padding-bottom: 14px;
        font-size: 0.67rem;
    }

    .gilant-footer-bottom-note {
        font-size: 0.6rem;
    }
}

/* =========================================================
   MÓVIL PEQUEÑO
========================================================= */

@media (max-width: 380px) {
    .gilant-footer-main {
        column-gap: 16px;
    }

    .gilant-footer-heading {
        letter-spacing: 0.12em;
    }

    .gilant-footer-navigation a {
        font-size: 0.76rem;
    }
}

/* =========================================================
   ACCESIBILIDAD
========================================================= */

@media (prefers-reduced-motion: reduce) {
    .gilant-footer *,
    .gilant-footer *::before,
    .gilant-footer *::after {
        scroll-behavior: auto !important;
        transition-duration: 0.01ms !important;
        animation-duration: 0.01ms !important;
    }
}
</style>
