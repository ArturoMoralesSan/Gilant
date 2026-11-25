
<script setup lang="ts">
import { ArrowDown, ArrowRight } from 'lucide-vue-next'
import { computed, onMounted, onUnmounted, ref } from 'vue'

interface Banner {
    title: string
    subtitle: string | null
    description: string | null
    image: string
    image_url: string
    alt_text: string | null
    link_url: string | null
    button_text: string | null
    open_in_new_tab: boolean
}

const props = withDefaults(
    defineProps<{
        banner: Banner | null
        scrollTarget?: string
        minHeight?: string
    }>(),
    {
        scrollTarget: '#nosotros',
        minHeight: '90vh',
    },
)

const heroRef = ref<HTMLElement | null>(null)
const backgroundRef = ref<HTMLElement | null>(null)

const FALLBACK_IMAGE = '/images/hero-background.jpeg'

const isVideo = (image?: string | null) =>
    Boolean(image && /\.(mp4|webm|mov)$/i.test(image))

const videoFailed = ref(false)

// Capas: imagen del banner (si existe) sobre la imagen de respaldo.
// Si la del banner no carga, el navegador muestra la de respaldo debajo.
const backgroundImage = computed(() => {
    const layers = [`url('${FALLBACK_IMAGE}')`]

    if (props.banner?.image_url) {
        layers.unshift(`url('${props.banner.image_url}')`)
    }

    return layers.join(', ')
})

let animationFrame = 0

function updateParallax() {
    cancelAnimationFrame(animationFrame)

    animationFrame = requestAnimationFrame(() => {
        if (!heroRef.value || !backgroundRef.value) return

        const rect = heroRef.value.getBoundingClientRect()
        const movement = rect.top * -0.12

        backgroundRef.value.style.setProperty(
            '--parallax-y',
            `${movement}px`,
        )
    })
}

onMounted(() => {
    window.addEventListener('scroll', updateParallax, { passive: true })
    window.addEventListener('resize', updateParallax)

    updateParallax()
})

onUnmounted(() => {
    window.removeEventListener('scroll', updateParallax)
    window.removeEventListener('resize', updateParallax)

    cancelAnimationFrame(animationFrame)
})
</script>

<template>
    <section
        ref="heroRef"
        class="gilant-hero"
        aria-labelledby="gilant-hero-title"
        :style="{ minHeight: props.minHeight }"
    >
        <!-- VIDEO DE FONDO -->
        <video
            v-if="props.banner?.image_url && isVideo(props.banner.image) && !videoFailed"
            ref="backgroundRef"
            class="gilant-hero-media gilant-hero-video"
            :src="props.banner.image_url"
            :poster="FALLBACK_IMAGE"
            :aria-label="props.banner.alt_text || props.banner.title"
            autoplay
            muted
            loop
            playsinline
            preload="auto"
            @error="videoFailed = true"
        />

        <!-- IMAGEN DE FONDO (con respaldo images/hero-background.jpeg) -->
        <div
            v-else
            ref="backgroundRef"
            class="gilant-hero-media gilant-hero-background"
            role="img"
            :aria-label="
                props.banner?.alt_text ||
                props.banner?.title ||
                'Arquitectura y proyectos de Grupo Gilant'
            "
            :style="{
                backgroundImage:
                    props.banner?.image_url && !isVideo(props.banner.image)
                        ? backgroundImage
                        : `url('${FALLBACK_IMAGE}')`,
            }"
        />

        <!-- CAPAS VISUALES -->
        <div class="gilant-hero-overlay" />
        <div class="gilant-hero-vignette" />

        <!-- CONTENIDO -->
        <div
            v-if="props.banner"
            class="gilant-container gilant-hero-container"
        >
            <div class="gilant-hero-content">

                <!-- SUBTÍTULO -->
                <div class="gilant-hero-overline">
                    <span class="gilant-hero-overline-line" />
                    <span>{{ props.banner.subtitle || 'GRUPO GILANT' }}</span>
                </div>

                <!-- TÍTULO -->
                <h1 id="gilant-hero-title" class="gilant-hero-title">
                    {{ props.banner.title }}
                </h1>

                <!-- DESCRIPCIÓN -->
                <p
                    v-if="props.banner.description"
                    class="gilant-hero-description"
                >
                    {{ props.banner.description }}
                </p>

                <!-- ACCIÓN -->
                <div
                    v-if="props.banner.link_url && props.banner.button_text"
                    class="gilant-hero-actions"
                >
                    <a
                        class="gilant-hero-link"
                        :href="props.banner.link_url"
                        :target="
                            props.banner.open_in_new_tab
                                ? '_blank'
                                : undefined
                        "
                        :rel="
                            props.banner.open_in_new_tab
                                ? 'noopener noreferrer'
                                : undefined
                        "
                    >
                        <span>{{ props.banner.button_text }}</span>

                        <span class="gilant-hero-link-icon">
                            <ArrowRight :size="16" />
                        </span>
                    </a>
                </div>
            </div>
        </div>

        <!-- PIE DEL HERO -->
        <div class="gilant-hero-bottom">
            <span class="gilant-hero-bottom-label">
                NATURALEZA · DISEÑO · DURABILIDAD
            </span>

            <a
                class="gilant-scroll-cue"
                :href="props.scrollTarget"
                aria-label="Desplazarse a la siguiente sección"
            >
                <span>DESCUBRIR</span>
                <ArrowDown :size="15" />
            </a>
        </div>
    </section>
</template>

<style scoped>
/* =========================================================
   GILANT HERO
   Arquitectura contemporánea / Hospitality de lujo
========================================================= */

.gilant-hero {
    --hero-cream: #F7F4EC;
    --hero-gold: #C49A5A;
    --hero-forest: #123D2D;

    position: relative;
    display: flex;
    align-items: center;

    min-height: min(850px, 94vh);
    min-height: min(850px, 94svh);

    width: 100%;
    overflow: hidden;
    isolation: isolate;

    color: var(--hero-cream);
    background: var(--hero-forest);
}

/* =========================================================
   IMAGEN Y VIDEO
========================================================= */

.gilant-hero-media {
    position: absolute;
    inset: -10% 0;

    z-index: -2;

    width: 100%;
    height: 120%;

    transform: translate3d(
        0,
        var(--parallax-y, 0px),
        0
    );

    will-change: transform;
}

.gilant-hero-background {
    background-position: center center;
    background-size: cover;
    background-repeat: no-repeat;

    filter: brightness(0.96) contrast(1.04) saturate(0.92);

    transform: translate3d(
        0,
        var(--parallax-y, 0px),
        0
    ) scale(1.04);
}

.gilant-hero-video {
    object-fit: cover;
    object-position: center;

    filter: brightness(0.96) contrast(1.04) saturate(0.92);

    transform: translate3d(
        0,
        var(--parallax-y, 0px),
        0
    ) scale(1.04);
}

/* =========================================================
   TRATAMIENTO FOTOGRÁFICO
========================================================= */

.gilant-hero-overlay {
    position: absolute;
    inset: 0;
    z-index: -1;

    background: rgba(8, 25, 19, 0.62);
}

.gilant-hero-vignette {
    position: absolute;
    inset: 0;
    z-index: -1;

    pointer-events: none;

    background:
        linear-gradient(
            0deg,
            rgba(5, 20, 14, 0.48) 0%,
            transparent 24%,
            transparent 75%,
            rgba(5, 20, 14, 0.16) 100%
        );
}

/* =========================================================
   CONTENEDOR
========================================================= */

.gilant-hero-container {
    position: relative;

    width: min(100% - 12%, 1440px);
    margin: 0 auto;

    padding-top: 130px;
    padding-bottom: 120px;
}

.gilant-hero-content {
    position: relative;

    width: min(100%, 740px);
    padding-left: 30px;

    animation: gilant-hero-reveal 1s ease both;
}

/* Línea arquitectónica */

.gilant-hero-content::before {
    position: absolute;
    top: 5px;
    bottom: 5px;
    left: 0;

    width: 1px;

    content: "";

    background: linear-gradient(
        180deg,
        var(--hero-gold),
        rgba(196, 154, 90, 0.12)
    );
}

/* =========================================================
   SUBTÍTULO
========================================================= */

.gilant-hero-overline {
    display: flex;
    align-items: center;
    gap: 12px;

    margin-bottom: 22px;

    color: #E0C69C;

    font-size: 10px;
    font-weight: 600;
    letter-spacing: 0.20em;
    line-height: 1.5;
    text-transform: uppercase;
}

.gilant-hero-overline-line {
    display: block;

    width: 32px;
    height: 1px;

    flex-shrink: 0;

    background: var(--hero-gold);
}

/* =========================================================
   TÍTULO PRINCIPAL
========================================================= */

.gilant-hero-title {
    max-width: 720px;

    margin: 0;

    color: var(--hero-cream);

    font-family:
        "Cormorant Garamond",
        "Bodoni MT",
        Georgia,
        serif;

    font-size: clamp(42px, 5.2vw, 72px);
    font-weight: 400;
    line-height: 1.05;
    letter-spacing: -0.035em;

    text-wrap: balance;

    text-shadow: 0 3px 30px rgba(0, 0, 0, 0.12);
}

/* =========================================================
   DESCRIPCIÓN
========================================================= */

.gilant-hero-description {
    max-width: 500px;

    margin: 22px 0 0;

    color: rgba(247, 244, 236, 0.88);

    font-size: clamp(14px, 1vw, 15px);
    font-weight: 300;
    line-height: 1.75;
    letter-spacing: 0.015em;

    text-wrap: pretty;
}

/* =========================================================
   BOTÓN
========================================================= */

.gilant-hero-actions {
    display: flex;
    align-items: center;

    margin-top: 30px;
}

.gilant-hero-link {
    display: inline-flex;
    align-items: center;
    gap: 16px;

    min-height: 44px;

    color: var(--hero-cream);

    font-size: 10px;
    font-weight: 600;
    letter-spacing: 0.16em;
    text-transform: uppercase;
    text-decoration: none;

    transition: color 0.3s ease;
}

.gilant-hero-link > span:first-child {
    padding-bottom: 6px;

    border-bottom: 1px solid rgba(196, 154, 90, 0.75);

    transition: border-color 0.3s ease;
}

.gilant-hero-link-icon {
    display: flex;
    align-items: center;
    justify-content: center;

    width: 38px;
    height: 38px;

    color: var(--hero-gold);

    border: 1px solid rgba(196, 154, 90, 0.65);
    border-radius: 50%;

    transition:
        color 0.3s ease,
        background 0.3s ease,
        transform 0.3s ease;
}

.gilant-hero-link:hover {
    color: #E5C99A;
}

.gilant-hero-link:hover > span:first-child {
    border-color: var(--hero-cream);
}

.gilant-hero-link:hover .gilant-hero-link-icon {
    color: var(--hero-forest);
    background: var(--hero-gold);
    transform: translateX(4px);
}

/* =========================================================
   PIE DEL HERO
========================================================= */

.gilant-hero-bottom {
    position: absolute;
    right: 6%;
    bottom: 30px;
    left: 6%;

    display: flex;
    align-items: center;
    justify-content: space-between;

    z-index: 2;

    pointer-events: none;
}

.gilant-hero-bottom-label {
    color: rgba(247, 244, 236, 0.72);

    font-size: 9px;
    font-weight: 500;
    letter-spacing: 0.17em;
    text-transform: uppercase;
}

.gilant-scroll-cue {
    display: inline-flex;
    align-items: center;
    gap: 10px;

    color: rgba(247, 244, 236, 0.85);

    font-size: 9px;
    font-weight: 600;
    letter-spacing: 0.15em;
    text-decoration: none;

    pointer-events: auto;

    transition: color 0.3s ease;
}

.gilant-scroll-cue svg {
    color: var(--hero-gold);

    transition: transform 0.3s ease;
}

.gilant-scroll-cue:hover {
    color: var(--hero-gold);
}

.gilant-scroll-cue:hover svg {
    transform: translateY(4px);
}

/* =========================================================
   ANIMACIÓN DE ENTRADA
========================================================= */

@keyframes gilant-hero-reveal {
    from {
        opacity: 0;
        transform: translateY(22px);
    }

    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* =========================================================
   TABLET
========================================================= */

@media (max-width: 850px) {
    .gilant-hero {
        min-height: min(780px, 90svh);
    }

    .gilant-hero-container {
        width: calc(100% - 9%);
        padding-top: 120px;
        padding-bottom: 110px;
    }

    .gilant-hero-content {
        width: min(100%, 640px);
    }

    .gilant-hero-title {
        font-size: clamp(40px, 7vw, 58px);
    }

    .gilant-hero-overlay {
        background: rgba(8, 25, 19, 0.66);
    }
}

/* =========================================================
   MÓVIL
========================================================= */

@media (max-width: 600px) {
    .gilant-hero {
        min-height: 82svh;
        align-items: center;
    }

    .gilant-hero-background {
        background-position: 58% center;
    }

    .gilant-hero-video {
        object-position: center;
    }

    .gilant-hero-container {
        width: calc(100% - 42px);
        padding-top: 110px;
        padding-bottom: 100px;
    }

    .gilant-hero-content {
        width: 100%;
        padding-left: 21px;
    }

    .gilant-hero-content::before {
        top: 3px;
        bottom: 3px;
    }

    .gilant-hero-overline {
        gap: 10px;
        margin-bottom: 18px;

        font-size: 9px;
        letter-spacing: 0.16em;
    }

    .gilant-hero-overline-line {
        width: 24px;
    }

    .gilant-hero-title {
        font-size: clamp(36px, 9vw, 48px);
        line-height: 1.08;
        letter-spacing: -0.035em;
    }

    .gilant-hero-description {
        max-width: 420px;
        margin-top: 18px;

        font-size: 13px;
        line-height: 1.7;
    }

    .gilant-hero-actions {
        margin-top: 24px;
    }

    .gilant-hero-link {
        gap: 13px;
        font-size: 9px;
    }

    .gilant-hero-link-icon {
        width: 36px;
        height: 36px;
    }

    .gilant-hero-bottom {
        right: 21px;
        bottom: 23px;
        left: 21px;
    }

    .gilant-hero-bottom-label {
        max-width: 180px;
        font-size: 8px;
        line-height: 1.6;
        letter-spacing: 0.11em;
    }

    .gilant-scroll-cue {
        gap: 7px;
        font-size: 8px;
    }
}

/* =========================================================
   MÓVILES PEQUEÑOS
========================================================= */

@media (max-width: 380px) {
    .gilant-hero-title {
        font-size: 34px;
    }

    .gilant-hero-description {
        font-size: 12px;
    }

    .gilant-hero-bottom-label {
        max-width: 145px;
    }
}

/* =========================================================
   ACCESIBILIDAD
========================================================= */

@media (prefers-reduced-motion: reduce) {
    .gilant-hero-media {
        transform: none !important;
        will-change: auto;
    }

    .gilant-hero-content {
        animation: none;
    }

    .gilant-hero-link,
    .gilant-hero-link-icon,
    .gilant-scroll-cue,
    .gilant-scroll-cue svg {
        transition: none;
    }
}
</style>
