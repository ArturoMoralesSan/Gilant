
<script setup lang="ts">
import { computed, onMounted, onUnmounted, ref, watch } from 'vue'
import { Head } from '@inertiajs/vue3'
import {
    ArrowUpRight,
    ChevronLeft,
    ChevronRight,
    X,
    Maximize2,
    TreeDeciduous,
} from 'lucide-vue-next'

import GilantHeader from '@/Components/GilantHeader.vue'
import GilantFooter from '@/Components/GilantFooter.vue'

import '../../css/gilant-landing.css'

// -----------------------------------------------------------------------------
// TIPOS
// -----------------------------------------------------------------------------

interface GalleryImage {
    id: number
    url: string
    alt: string
    is_primary: boolean
}

interface Service {
    id: number
    name: string
    slug: string
    description: string | null
    gallery: GalleryImage[]
    main_image: string | null
}

const props = defineProps<{
    service: Service
}>()

// -----------------------------------------------------------------------------
// SEO
// -----------------------------------------------------------------------------

const siteUrl = (
    import.meta.env.VITE_APP_URL || 'https://grupogilant.com'
).replace(/\/$/, '')

function absoluteUrl(url: string | null | undefined): string | undefined {
    if (!url) return undefined

    try {
        return new URL(url, `${siteUrl}/`).toString()
    } catch {
        return undefined
    }
}

const canonicalUrl = computed(
    () => `${siteUrl}/servicios/${encodeURIComponent(props.service.slug)}`
)

const seoTitle = computed(
    () => `${props.service.name} | Grupo Gilant`
)

const seoDescription = computed(() => {
    const description =
        props.service.description?.replace(/\s+/g, ' ').trim() ||
        `Conoce el servicio de ${props.service.name} de Grupo Gilant y descubre nuestras soluciones en construcción con madera.`

    return description.length > 160
        ? `${description.slice(0, 157).trimEnd()}...`
        : description
})

// -----------------------------------------------------------------------------
// GALERÍA
// -----------------------------------------------------------------------------

const gallery = computed(() => props.service.gallery || [])

const galleryCount = computed(() => gallery.value.length)

const selectedImage = ref<number | null>(null)

const showGallery = ref(false)

const currentImage = computed<GalleryImage | null>(() => {
    if (selectedImage.value === null) return null

    return gallery.value.find(
        image => image.id === selectedImage.value
    ) || null
})

const galleryIndex = computed(() => {
    if (!currentImage.value) return -1

    return gallery.value.findIndex(
        image => image.id === currentImage.value?.id
    )
})

const hasMultipleImages = computed(() => galleryCount.value > 1)

const displayImage = computed(
    () => currentImage.value?.url || props.service.main_image || null
)

const socialImage = computed(
    () =>
        absoluteUrl(currentImage.value?.url) ||
        absoluteUrl(props.service.main_image) ||
        `${siteUrl}/images/og-default.jpg`
)

const socialImageAlt = computed(
    () => currentImage.value?.alt || props.service.name
)

function selectImage(image: GalleryImage) {
    selectedImage.value = image.id
}

function openGallery() {
    if (!galleryCount.value) return

    showGallery.value = true
}

function closeGallery() {
    showGallery.value = false
}

function changeImage(direction: number) {
    if (!galleryCount.value) return

    const currentIndex = galleryIndex.value >= 0
        ? galleryIndex.value
        : 0

    const nextIndex =
        (currentIndex + direction + galleryCount.value) %
        galleryCount.value

    selectedImage.value = gallery.value[nextIndex].id
}

// -----------------------------------------------------------------------------
// DATOS ESTRUCTURADOS
// -----------------------------------------------------------------------------

const serviceSchema = computed(() => ({
    '@context': 'https://schema.org',
    '@type': 'Service',
    '@id': `${canonicalUrl.value}#service`,
    name: props.service.name,
    description: seoDescription.value,
    ...(socialImage.value ? { image: socialImage.value } : {}),
    url: canonicalUrl.value,

    provider: {
        '@type': 'Organization',
        name: 'Grupo Gilant',
        url: `${siteUrl}/`,
    },

    areaServed: {
        '@type': 'Country',
        name: 'México',
    },
}))

const serviceSchemaJson = computed(
    () => JSON.stringify(serviceSchema.value)
)

// -----------------------------------------------------------------------------
// SWIPE TÁCTIL
// -----------------------------------------------------------------------------

interface PointerPosition {
    x: number
    y: number
    pointerId: number
}

const mainPointerStart = ref<PointerPosition | null>(null)
const lightboxPointerStart = ref<PointerPosition | null>(null)

const suppressClick = ref(false)

let clickResetTimer: ReturnType<typeof setTimeout> | null = null

const SWIPE_THRESHOLD = 50

function handlePointerStart(
    event: PointerEvent,
    area: 'main' | 'lightbox',
) {
    if (event.pointerType === 'mouse' && event.button !== 0) return

    const target = event.target

    if (target instanceof Element && target.closest('button')) return

    const position: PointerPosition = {
        x: event.clientX,
        y: event.clientY,
        pointerId: event.pointerId,
    }

    if (area === 'main') {
        mainPointerStart.value = position
    } else {
        lightboxPointerStart.value = position
    }

    const element = event.currentTarget

    if (element instanceof HTMLElement) {
        try {
            element.setPointerCapture(event.pointerId)
        } catch {
            // El navegador puede no permitir capturar el puntero.
        }
    }
}

function handlePointerEnd(
    event: PointerEvent,
    area: 'main' | 'lightbox',
) {
    const start = area === 'main'
        ? mainPointerStart.value
        : lightboxPointerStart.value

    if (!start || start.pointerId !== event.pointerId) return

    if (area === 'main') {
        mainPointerStart.value = null
    } else {
        lightboxPointerStart.value = null
    }

    const deltaX = event.clientX - start.x
    const deltaY = event.clientY - start.y

    if (
        Math.abs(deltaX) < SWIPE_THRESHOLD ||
        Math.abs(deltaX) < Math.abs(deltaY) * 1.2
    ) {
        return
    }

    changeImage(deltaX < 0 ? 1 : -1)

    if (area === 'main') {
        suppressClick.value = true

        if (clickResetTimer) {
            clearTimeout(clickResetTimer)
        }

        clickResetTimer = setTimeout(() => {
            suppressClick.value = false
            clickResetTimer = null
        }, 500)
    }
}

function handlePointerCancel(
    event: PointerEvent,
    area: 'main' | 'lightbox',
) {
    const start = area === 'main'
        ? mainPointerStart.value
        : lightboxPointerStart.value

    if (!start || start.pointerId !== event.pointerId) return

    if (area === 'main') {
        mainPointerStart.value = null
    } else {
        lightboxPointerStart.value = null
    }
}

function handleImageClick() {
    if (suppressClick.value) {
        suppressClick.value = false

        if (clickResetTimer) {
            clearTimeout(clickResetTimer)
            clickResetTimer = null
        }

        return
    }

    openGallery()
}

// -----------------------------------------------------------------------------
// TECLADO
// -----------------------------------------------------------------------------

function handleKeydown(event: KeyboardEvent) {
    if (!showGallery.value) return

    if (event.key === 'Escape') {
        closeGallery()
    }

    if (event.key === 'ArrowRight') {
        changeImage(1)
    }

    if (event.key === 'ArrowLeft') {
        changeImage(-1)
    }
}

// -----------------------------------------------------------------------------
// WATCHERS
// -----------------------------------------------------------------------------

watch(
    () => props.service.gallery,
    images => {
        const availableImages = images || []

        if (!availableImages.length) {
            selectedImage.value = null
            showGallery.value = false
            return
        }

        const currentExists = availableImages.some(
            image => image.id === selectedImage.value
        )

        if (!currentExists) {
            const primary =
                availableImages.find(image => image.is_primary) ||
                availableImages[0]

            selectedImage.value = primary.id
        }
    },
    { immediate: true, deep: true }
)

let previousBodyOverflow = ''

watch(showGallery, isOpen => {
    if (typeof document === 'undefined') return

    if (isOpen) {
        previousBodyOverflow = document.body.style.overflow
        document.body.style.overflow = 'hidden'
    } else {
        document.body.style.overflow = previousBodyOverflow
    }
})

// -----------------------------------------------------------------------------
// CONTACTO
// -----------------------------------------------------------------------------

const contactUrl = computed(
    () => `/?service=${props.service.id}#contacto`
)

// -----------------------------------------------------------------------------
// CICLO DE VIDA
// -----------------------------------------------------------------------------

onMounted(() => {
    window.addEventListener('keydown', handleKeydown)
})

onUnmounted(() => {
    window.removeEventListener('keydown', handleKeydown)

    if (clickResetTimer) {
        clearTimeout(clickResetTimer)
    }

    document.body.style.overflow = previousBodyOverflow
})
</script>

<template>
    <Head>
        <title>{{ seoTitle }}</title>

        <meta
            head-key="description"
            name="description"
            :content="seoDescription"
        />

        <meta
            head-key="robots"
            name="robots"
            content="index, follow"
        />

        <link
            head-key="canonical"
            rel="canonical"
            :href="canonicalUrl"
        />

        <meta
            head-key="theme-color"
            name="theme-color"
            content="#f5f3ec"
        />

        <meta
            head-key="author"
            name="author"
            content="Grupo Gilant"
        />

        <meta
            head-key="language"
            name="language"
            content="es-MX"
        />

        <!-- OPEN GRAPH -->
        <meta
            head-key="og:type"
            property="og:type"
            content="website"
        />

        <meta
            head-key="og:site_name"
            property="og:site_name"
            content="Grupo Gilant"
        />

        <meta
            head-key="og:title"
            property="og:title"
            :content="seoTitle"
        />

        <meta
            head-key="og:description"
            property="og:description"
            :content="seoDescription"
        />

        <meta
            head-key="og:url"
            property="og:url"
            :content="canonicalUrl"
        />

        <meta
            head-key="og:image"
            property="og:image"
            :content="socialImage"
        />

        <meta
            head-key="og:image:alt"
            property="og:image:alt"
            :content="socialImageAlt"
        />

        <meta
            head-key="og:locale"
            property="og:locale"
            content="es_MX"
        />

        <!-- TWITTER -->
        <meta
            head-key="twitter:card"
            name="twitter:card"
            content="summary_large_image"
        />

        <meta
            head-key="twitter:title"
            name="twitter:title"
            :content="seoTitle"
        />

        <meta
            head-key="twitter:description"
            name="twitter:description"
            :content="seoDescription"
        />

        <meta
            head-key="twitter:image"
            name="twitter:image"
            :content="socialImage"
        />

        <meta
            head-key="twitter:image:alt"
            name="twitter:image:alt"
            :content="socialImageAlt"
        />

        <!-- DATOS ESTRUCTURADOS -->
        <component
            :is="'script'"
            type="application/ld+json"
            head-key="service-schema"
            v-html="serviceSchemaJson"
        />
    </Head>

    <div class="gilant-site service-detail">
        <GilantHeader />

        <main>
            <!-- DETALLE -->
            <section class="service-detail-section">
                <div class="gilant-container">

                    <div class="service-detail-heading">
                        <span class="service-section-label">
                            <span></span>
                            SERVICIOS / SOLUCIONES
                        </span>

                        <p>
                            Soluciones arquitectónicas en madera para
                            proyectos que combinan diseño, funcionalidad
                            y conexión con el entorno.
                        </p>
                    </div>

                    <div class="service-detail-grid">

                        <!-- GALERÍA -->
                        <div class="service-gallery-column">

                            <div
                                v-if="displayImage"
                                class="service-main-image"
                                @pointerdown="handlePointerStart($event, 'main')"
                                @pointerup="handlePointerEnd($event, 'main')"
                                @pointercancel="handlePointerCancel($event, 'main')"
                                @click="handleImageClick"
                            >
                                <img
                                    :src="displayImage"
                                    :alt="currentImage?.alt || service.name"
                                    draggable="false"
                                />

                                <button
                                    v-if="galleryCount"
                                    type="button"
                                    class="service-expand-button"
                                    aria-label="Ampliar imagen"
                                    @click.stop="openGallery"
                                >
                                    <Maximize2 :size="16" />
                                </button>

                                <template v-if="hasMultipleImages">
                                    <button
                                        type="button"
                                        class="service-image-arrow service-image-prev"
                                        aria-label="Imagen anterior"
                                        @click.stop="changeImage(-1)"
                                    >
                                        <ChevronLeft :size="19" />
                                    </button>

                                    <button
                                        type="button"
                                        class="service-image-arrow service-image-next"
                                        aria-label="Imagen siguiente"
                                        @click.stop="changeImage(1)"
                                    >
                                        <ChevronRight :size="19" />
                                    </button>
                                </template>
                            </div>

                            <div
                                v-else
                                class="service-image-placeholder"
                            >
                                <TreeDeciduous :size="42" />
                                <span>Imagen del servicio</span>
                            </div>

                            <!-- MINIATURAS -->
                            <div
                                v-if="galleryCount > 1"
                                class="service-thumbnails"
                            >
                                <button
                                    v-for="(image, index) in gallery"
                                    :key="image.id"
                                    type="button"
                                    class="service-thumbnail"
                                    :class="{
                                        'is-active': selectedImage === image.id,
                                    }"
                                    :aria-label="`Ver imagen ${index + 1}`"
                                    :aria-pressed="selectedImage === image.id"
                                    @click="selectImage(image)"
                                >
                                    <img
                                        :src="image.url"
                                        :alt="
                                            image.alt ||
                                            `${service.name} - imagen ${index + 1}`
                                        "
                                        loading="lazy"
                                    />
                                </button>
                            </div>

                            <div
                                v-if="galleryCount"
                                class="service-gallery-footer"
                            >
                                <span>GALERÍA DEL SERVICIO</span>

                                <span class="service-gallery-counter">
                                    {{ String(galleryIndex + 1).padStart(2, '0') }}
                                    <span>/</span>
                                    {{ String(galleryCount).padStart(2, '0') }}
                                </span>
                            </div>
                        </div>

                        <!-- INFORMACIÓN -->
                        <div class="service-information">

                            <div class="service-information-top">
                                <span class="service-eyebrow">
                                    GRUPO GILANT
                                </span>

                                <span class="service-index">
                                    SOLUCIONES ESPECIALIZADAS
                                </span>
                            </div>

                            <h1>{{ service.name }}</h1>

                            <div class="service-title-decoration">
                                <span></span>
                            </div>

                            <div
                                v-if="service.description"
                                class="service-description"
                            >
                                {{ service.description }}
                            </div>

                            <div
                                v-else
                                class="service-description"
                            >
                                En Grupo Gilant desarrollamos soluciones
                                especializadas en madera estructural y obra
                                exterior, integrando diseño, planeación y
                                conocimiento técnico para responder a las
                                necesidades de cada proyecto.
                            </div>

                            <div class="service-information-divider"></div>

                            <div class="service-concept">
                                <div class="service-concept-icon">
                                    <TreeDeciduous :size="21" />
                                </div>

                                <div>
                                    <span>ARQUITECTURA EN MADERA</span>
                                    <p>
                                        Diseñamos y desarrollamos soluciones
                                        en madera estructural y obra exterior,
                                        acompañando cada proyecto desde la
                                        planeación hasta su instalación.
                                    </p>
                                </div>
                            </div>

                            <a
                                :href="contactUrl"
                                class="service-contact-link"
                            >
                                <span>Solicitar información</span>

                                <span class="service-contact-icon">
                                    <ArrowUpRight :size="18" />
                                </span>
                            </a>

                            <div class="service-information-foot">
                                <span>GRUPO GILANT DEL NOROESTE</span>
                                <span>NATURALEZA · DISEÑO · DURABILIDAD</span>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </main>

        <GilantFooter />

        <!-- LIGHTBOX -->
        <Transition name="service-lightbox">
            <div
                v-if="showGallery && currentImage"
                class="service-lightbox"
                role="dialog"
                aria-modal="true"
                :aria-label="`Galería de ${service.name}`"
                @click.self="closeGallery"
            >
                <button
                    type="button"
                    class="service-lightbox-close"
                    aria-label="Cerrar galería"
                    @click="closeGallery"
                >
                    <X :size="23" />
                </button>

                <div class="service-lightbox-content">

                    <div class="service-lightbox-heading">
                        <span>GRUPO GILANT</span>
                        <p>{{ service.name }}</p>
                    </div>

                    <div
                        class="service-lightbox-stage"
                        @pointerdown="handlePointerStart($event, 'lightbox')"
                        @pointerup="handlePointerEnd($event, 'lightbox')"
                        @pointercancel="handlePointerCancel($event, 'lightbox')"
                    >
                        <button
                            v-if="hasMultipleImages"
                            type="button"
                            class="service-lightbox-arrow service-lightbox-prev"
                            aria-label="Imagen anterior"
                            @click="changeImage(-1)"
                        >
                            <ChevronLeft :size="26" />
                        </button>

                        <img
                            :src="currentImage.url"
                            :alt="currentImage.alt || service.name"
                            draggable="false"
                        />

                        <button
                            v-if="hasMultipleImages"
                            type="button"
                            class="service-lightbox-arrow service-lightbox-next"
                            aria-label="Imagen siguiente"
                            @click="changeImage(1)"
                        >
                            <ChevronRight :size="26" />
                        </button>
                    </div>

                    <div class="service-lightbox-footer">
                        <span>{{ currentImage.alt || service.name }}</span>

                        <span>
                            {{ String(galleryIndex + 1).padStart(2, '0') }}
                            /
                            {{ String(galleryCount).padStart(2, '0') }}
                        </span>
                    </div>

                    <div
                        v-if="galleryCount > 1"
                        class="service-lightbox-thumbnails"
                    >
                        <button
                            v-for="image in gallery"
                            :key="image.id"
                            type="button"
                            :class="{
                                'is-active': selectedImage === image.id,
                            }"
                            :aria-label="`Ver ${image.alt || 'imagen'}`"
                            :aria-pressed="selectedImage === image.id"
                            @click="selectImage(image)"
                        >
                            <img
                                :src="image.url"
                                :alt="image.alt || service.name"
                            />
                        </button>
                    </div>
                </div>
            </div>
        </Transition>
    </div>
</template>

<style scoped>
/* -------------------------------------------------------------------------- */
/* CONTENEDOR GENERAL                                                          */
/* -------------------------------------------------------------------------- */

.service-detail {
    background: #fffefa;
    color: #25352d;
}

/* -------------------------------------------------------------------------- */
/* ENCABEZADO                                                                   */
/* -------------------------------------------------------------------------- */

.service-detail-section {
    padding: clamp(115px, 13vh, 155px) 0 100px;
    background: #fffefa;
}

.service-detail-heading {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 30px;
    margin-bottom: 44px;
    padding-bottom: 22px;
    border-bottom: 1px solid #e9e9e1;
}

.service-section-label {
    display: inline-flex;
    align-items: center;
    gap: 11px;
    color: #737d43;
    font-size: 10px;
    font-weight: 700;
    letter-spacing: .17em;
}

.service-section-label > span {
    width: 22px;
    height: 1px;
    background: #737d43;
}

.service-detail-heading p {
    max-width: 380px;
    margin: 0;
    color: #92978d;
    font-size: 12px;
    line-height: 1.7;
    text-align: right;
}

/* -------------------------------------------------------------------------- */
/* DISTRIBUCIÓN PRINCIPAL                                                      */
/* -------------------------------------------------------------------------- */

.service-detail-grid {
    display: grid;
    grid-template-columns: minmax(0, 1.08fr) minmax(300px, .82fr);
    align-items: center;
    gap: clamp(48px, 7vw, 105px);
}

.service-gallery-column {
    min-width: 0;
    width: 100%;
    max-width: 650px;
    margin: 0 auto;
}

/* -------------------------------------------------------------------------- */
/* IMAGEN PRINCIPAL                                                            */
/* -------------------------------------------------------------------------- */

.service-main-image {
    position: relative;
    overflow: hidden;
    width: 100%;
    max-height: 490px;
    aspect-ratio: 1.2 / 1;
    background: #eeeee8;
    cursor: zoom-in;
    touch-action: pan-y;
    user-select: none;
    -webkit-user-select: none;
}

.service-main-image > img {
    display: block;
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform .7s cubic-bezier(.2, .7, .2, 1);
    -webkit-user-drag: none;
}

.service-main-image:hover > img {
    transform: scale(1.025);
}

.service-expand-button {
    position: absolute;
    right: 17px;
    bottom: 17px;
    display: grid;
    width: 38px;
    height: 38px;
    place-items: center;
    border: 1px solid rgb(255 255 255 / 45%);
    background: rgb(25 44 36 / 72%);
    color: white;
    cursor: pointer;
    transition: background .25s ease;
}

.service-expand-button:hover {
    background: #737d43;
}

.service-image-arrow {
    position: absolute;
    z-index: 2;
    top: 50%;
    display: grid;
    width: 38px;
    height: 38px;
    padding: 0;
    place-items: center;
    border: 1px solid rgb(255 255 255 / 45%);
    border-radius: 50%;
    background: rgb(25 44 36 / 68%);
    color: white;
    cursor: pointer;
    transform: translateY(-50%);
    transition: background .25s ease, border-color .25s ease;
}

.service-image-arrow:hover {
    border-color: white;
    background: #737d43;
}

.service-image-prev {
    left: 15px;
}

.service-image-next {
    right: 15px;
}

.service-image-placeholder {
    display: flex;
    width: 100%;
    max-height: 490px;
    aspect-ratio: 1.2 / 1;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 13px;
    background: #f1f0e9;
    color: #737d43;
}

.service-image-placeholder span {
    color: #92978d;
    font-size: 12px;
}

/* -------------------------------------------------------------------------- */
/* MINIATURAS                                                                   */
/* -------------------------------------------------------------------------- */

.service-thumbnails {
    display: flex;
    gap: 9px;
    margin-top: 13px;
    overflow-x: auto;
    padding: 2px 0 5px;
    scrollbar-width: thin;
    scrollbar-color: #c8cebd transparent;
}

.service-thumbnail {
    position: relative;
    overflow: hidden;
    width: 76px;
    height: 57px;
    flex: 0 0 auto;
    padding: 0;
    border: 1px solid transparent;
    background: #eeeee8;
    cursor: pointer;
    opacity: .65;
    transition: border-color .25s ease, opacity .25s ease;
}

.service-thumbnail:hover,
.service-thumbnail.is-active {
    border-color: #737d43;
    opacity: 1;
}

.service-thumbnail img {
    display: block;
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.service-gallery-footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-top: 18px;
    padding-top: 13px;
    border-top: 1px solid #e9e9e1;
    color: #9a9e94;
    font-size: 9px;
    font-weight: 700;
    letter-spacing: .14em;
}

.service-gallery-counter {
    display: flex;
    gap: 7px;
    color: #243b30;
    font-size: 11px;
    letter-spacing: .06em;
}

.service-gallery-counter span {
    color: #b5b9ae;
}

/* -------------------------------------------------------------------------- */
/* INFORMACIÓN DEL SERVICIO                                                    */
/* -------------------------------------------------------------------------- */

.service-information {
    width: 100%;
    max-width: 480px;
    padding: 12px 0;
}

.service-information-top {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    justify-content: space-between;
    gap: 15px;
    margin-bottom: 27px;
}

.service-eyebrow {
    color: #737d43;
    font-size: 10px;
    font-weight: 700;
    letter-spacing: .2em;
}

.service-index {
    color: #a1a59b;
    font-size: 9px;
    font-weight: 600;
    letter-spacing: .12em;
}

.service-information h1 {
    margin: 0;
    color: #243b30;
    font-family: Manrope, sans-serif;
    font-size: clamp(32px, 3.5vw, 47px);
    font-weight: 500;
    line-height: 1.16;
    letter-spacing: -.045em;
    overflow-wrap: anywhere;
}

.service-title-decoration {
    display: flex;
    align-items: center;
    gap: 12px;
    margin: 27px 0 25px;
}

.service-title-decoration span {
    width: 43px;
    height: 2px;
    background: #737d43;
}

.service-description {
    color: #737c70;
    font-size: 14px;
    line-height: 1.95;
    white-space: pre-line;
}

.service-information-divider {
    height: 1px;
    margin: 32px 0;
    background: #e9e9e1;
}

/* -------------------------------------------------------------------------- */
/* CONCEPTO                                                                     */
/* -------------------------------------------------------------------------- */

.service-concept {
    display: flex;
    align-items: flex-start;
    gap: 15px;
}

.service-concept-icon {
    display: grid;
    width: 42px;
    height: 42px;
    flex: 0 0 auto;
    place-items: center;
    background: #f2f2eb;
    color: #737d43;
}

.service-concept > div:last-child > span {
    display: block;
    margin-bottom: 8px;
    color: #737d43;
    font-size: 9px;
    font-weight: 700;
    letter-spacing: .14em;
}

.service-concept p {
    margin: 0;
    color: #81887d;
    font-size: 12px;
    line-height: 1.8;
}

/* -------------------------------------------------------------------------- */
/* CONTACTO                                                                     */
/* -------------------------------------------------------------------------- */

.service-contact-link {
    display: flex;
    width: 100%;
    min-height: 58px;
    align-items: center;
    justify-content: space-between;
    gap: 15px;
    margin-top: 35px;
    padding: 0 17px 0 21px;
    border: 1px solid #243b30;
    background: #243b30;
    color: #fffefa;
    font-size: 12px;
    font-weight: 600;
    text-decoration: none;
    transition: background .25s ease, border-color .25s ease;
}

.service-contact-link:hover {
    border-color: #737d43;
    background: #737d43;
}

.service-contact-icon {
    display: grid;
    width: 31px;
    height: 31px;
    place-items: center;
    border: 1px solid rgb(255 255 255 / 30%);
    border-radius: 50%;
    transition: transform .25s ease;
}

.service-contact-link:hover .service-contact-icon {
    transform: translate(2px, -2px);
}

.service-information-foot {
    display: flex;
    flex-wrap: wrap;
    justify-content: space-between;
    gap: 10px;
    margin-top: 25px;
    color: #a1a59b;
    font-size: 8px;
    font-weight: 600;
    letter-spacing: .1em;
}

/* -------------------------------------------------------------------------- */
/* LIGHTBOX                                                                     */
/* -------------------------------------------------------------------------- */

.service-lightbox {
    position: fixed;
    z-index: 99999;
    inset: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    width: 100vw;
    height: 100vh;
    height: 100dvh;
    padding: 45px;
    overflow: hidden;
    background: rgb(17 29 23 / 97%);
}

.service-lightbox-close {
    position: absolute;
    z-index: 5;
    top: 22px;
    right: 25px;
    display: grid;
    width: 42px;
    height: 42px;
    place-items: center;
    border: 1px solid rgb(255 255 255 / 25%);
    border-radius: 50%;
    background: transparent;
    color: white;
    cursor: pointer;
    transition: background .25s ease, border-color .25s ease;
}

.service-lightbox-close:hover {
    border-color: white;
    background: rgb(255 255 255 / 12%);
}

.service-lightbox-content {
    display: flex;
    width: min(1000px, 100%);
    max-height: 100%;
    flex-direction: column;
    align-items: center;
}

.service-lightbox-heading {
    width: 100%;
    margin-bottom: 17px;
}

.service-lightbox-heading > span {
    color: #aab58e;
    font-size: 9px;
    font-weight: 700;
    letter-spacing: .2em;
}

.service-lightbox-heading p {
    margin: 7px 0 0;
    color: white;
    font-size: 15px;
    font-weight: 500;
}

.service-lightbox-stage {
    position: relative;
    display: flex;
    width: 100%;
    height: min(65vh, 680px);
    align-items: center;
    justify-content: center;
    touch-action: pan-y;
    user-select: none;
    -webkit-user-select: none;
}

.service-lightbox-stage > img {
    display: block;
    max-width: 100%;
    max-height: 100%;
    object-fit: contain;
    -webkit-user-drag: none;
}

.service-lightbox-arrow {
    position: absolute;
    z-index: 2;
    top: 50%;
    display: grid;
    width: 43px;
    height: 43px;
    place-items: center;
    border: 1px solid rgb(255 255 255 / 30%);
    border-radius: 50%;
    background: rgb(255 255 255 / 8%);
    color: white;
    cursor: pointer;
    transform: translateY(-50%);
    transition: background .25s ease;
}

.service-lightbox-arrow:hover {
    background: rgb(255 255 255 / 20%);
}

.service-lightbox-prev {
    left: 0;
}

.service-lightbox-next {
    right: 0;
}

.service-lightbox-footer {
    display: flex;
    width: 100%;
    justify-content: space-between;
    gap: 20px;
    padding: 16px 0;
    border-bottom: 1px solid rgb(255 255 255 / 15%);
    color: rgb(255 255 255 / 65%);
    font-size: 11px;
}

.service-lightbox-thumbnails {
    display: flex;
    max-width: 100%;
    gap: 9px;
    overflow-x: auto;
    padding: 15px 0 5px;
}

.service-lightbox-thumbnails button {
    overflow: hidden;
    width: 65px;
    height: 48px;
    flex: 0 0 auto;
    padding: 0;
    border: 1px solid transparent;
    background: transparent;
    cursor: pointer;
    opacity: .45;
    transition: opacity .2s ease, border-color .2s ease;
}

.service-lightbox-thumbnails button.is-active {
    border-color: #dce2d4;
    opacity: 1;
}

.service-lightbox-thumbnails img {
    display: block;
    width: 100%;
    height: 100%;
    object-fit: cover;
}

/* -------------------------------------------------------------------------- */
/* TRANSICIÓN                                                                   */
/* -------------------------------------------------------------------------- */

.service-lightbox-enter-active,
.service-lightbox-leave-active {
    transition: opacity .25s ease;
}

.service-lightbox-enter-from,
.service-lightbox-leave-to {
    opacity: 0;
}

/* -------------------------------------------------------------------------- */
/* RESPONSIVE                                                                   */
/* -------------------------------------------------------------------------- */

@media (max-width: 1000px) {
    .service-detail-grid {
        grid-template-columns: minmax(0, 1fr) minmax(280px, .85fr);
        gap: 40px;
    }

    .service-main-image {
        max-height: 420px;
    }

    .service-information h1 {
        font-size: clamp(30px, 4vw, 40px);
    }
}

@media (max-width: 800px) {
    .service-detail-grid {
        grid-template-columns: 1fr;
        gap: 42px;
    }

    .service-gallery-column {
        max-width: 620px;
    }

    .service-information {
        max-width: 620px;
        margin: 0 auto;
    }

    .service-main-image {
        max-height: 480px;
    }

    .service-detail-section {
        padding-bottom: 70px;
    }
}

@media (max-width: 600px) {
    .service-detail-section {
        padding-top: 105px;
    }

    .service-detail-heading {
        align-items: flex-start;
        flex-direction: column;
        gap: 12px;
        margin-bottom: 28px;
    }

    .service-detail-heading p {
        text-align: left;
    }

    .service-detail-grid {
        gap: 35px;
    }

    .service-main-image {
        aspect-ratio: 1.05 / 1;
    }

    .service-image-arrow {
        width: 34px;
        height: 34px;
    }

    .service-image-prev {
        left: 10px;
    }

    .service-image-next {
        right: 10px;
    }

    .service-expand-button {
        right: 11px;
        bottom: 11px;
        width: 34px;
        height: 34px;
    }

    .service-thumbnail {
        width: 65px;
        height: 49px;
    }

    .service-information {
        padding: 0;
    }

    .service-information-top {
        margin-bottom: 20px;
    }

    .service-information h1 {
        font-size: 34px;
    }

    .service-title-decoration {
        margin: 22px 0;
    }

    .service-description {
        font-size: 13px;
    }

    .service-information-divider {
        margin: 25px 0;
    }

    .service-information-foot {
        flex-direction: column;
    }

    .service-lightbox {
        padding: 65px 13px 20px;
    }

    .service-lightbox-stage {
        height: 55vh;
    }

    .service-lightbox-arrow {
        width: 35px;
        height: 35px;
    }

    .service-lightbox-close {
        top: 13px;
        right: 13px;
        width: 38px;
        height: 38px;
    }

    .service-lightbox-thumbnails button {
        width: 55px;
        height: 42px;
    }
}

/* -------------------------------------------------------------------------- */
/* ACCESIBILIDAD                                                                */
/* -------------------------------------------------------------------------- */

@media (prefers-reduced-motion: reduce) {
    .service-detail *,
    .service-detail *::before,
    .service-detail *::after {
        scroll-behavior: auto !important;
        animation-duration: .01ms !important;
        animation-iteration-count: 1 !important;
        transition-duration: .01ms !important;
    }
}
</style>
