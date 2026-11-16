
<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3'
import {
    ArrowRight,
    ArrowUpRight,
    CheckCircle2,
    ChevronDown,
    Facebook,
    Instagram,
    Mail,
    MapPin,
    Phone,
    TreeDeciduous,
    X,
} from 'lucide-vue-next'
import { computed, nextTick, onMounted, ref } from 'vue'

import GilantFooter from '@/components/GilantFooter.vue'
import GilantHeader from '@/components/GilantHeader.vue'
import GilantHero from '@/components/GilantHero.vue'
import ItemCards from '@/components/ItemCards.vue'

import '../../css/gilant-landing.css'

// -----------------------------------------------------------------------------
// TIPOS
// -----------------------------------------------------------------------------

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

interface ProductVariant {
    id: number
    name: string
}

interface Product {
    id: number
    name: string
    slug: string
    short_description: string | null
    material: string | null
    treatment: string | null
    retention: string | null
    retention_unit: string | null
    image_url: string | null
    image_alt: string
    variants: ProductVariant[]
}

interface QuoteOption {
    id: number
    name: string
}

interface QuoteOptions {
    products: QuoteOption[]
    services: QuoteOption[]
}

interface Service {
    id: number
    name: string
    slug: string
    description: string | null
}

interface Project {
    id: number
    name: string
    slug: string
    short_description: string | null
    image_url: string | null
    image_alt: string
}

interface Client {
    id: number
    name: string
    logo_url: string | null
}

interface Contact {
    email: string | null
    phone_primary: string | null
    phone_secondary: string | null
    phone_tertiary: string | null
    address: string | null
    whatsapp: string | null
    schedule_weekdays: string | null
    schedule_saturday: string | null
    facebook_url: string | null
    instagram_url: string | null
}

interface AboutGalleryImage {
    id: number
    file: string
    url: string
    alt: string | null
    is_primary: boolean
    sort_order: number
}

interface About {
    id: number
    history: string | null
    gallery_images: AboutGalleryImage[]
}

const props = defineProps<{
    banner: Banner | null
    products: Product[]
    services: Service[]
    projects: Project[]
    clients: Client[]
    contact: Contact | null
    about: About | null
    quoteOptions: QuoteOptions
}>()

// -----------------------------------------------------------------------------
// SEO
// -----------------------------------------------------------------------------

const seoTitle = 'Grupo Gilant | Ingeniería y soluciones en madera'

const seoDescription =
    'En Grupo Gilant desarrollamos soluciones arquitectónicas y constructivas en madera para proyectos residenciales, turísticos y comerciales. Diseñamos y ejecutamos estructuras, decks, muelles, pérgolas, puentes y espacios de alto valor.'

const canonicalUrl = 'https://gilant.com.mx/'

const ogImage = 'https://gilant.com.mx/og-default.jpg'

const organizationSchema = computed(() => ({
    '@context': 'https://schema.org',
    '@type': 'Organization',
    name: 'Grupo Gilant',
    url: canonicalUrl,
    logo: ogImage,
    description: seoDescription,
    email: props.contact?.email || undefined,
    telephone: props.contact?.phone_primary || undefined,
    sameAs: [
        props.contact?.facebook_url,
        props.contact?.instagram_url,
    ].filter((url): url is string => Boolean(url)),
}))

const organizationSchemaJson = computed(() =>
    JSON.stringify(organizationSchema.value),
)

// -----------------------------------------------------------------------------
// COTIZACIONES
// -----------------------------------------------------------------------------

const quoteFormRef = ref<HTMLFormElement | null>(null)
const selectedItem = ref('')
const toastVisible = ref(false)
const toastMessage = ref('')
const toastType = ref<'success' | 'error'>('success')

let toastTimeout: ReturnType<typeof setTimeout> | undefined

const quoteForm = useForm({
    name: '',
    company: '',
    email: '',
    phone: '',
    product_id: null as number | null,
    service_id: null as number | null,
    item_name: '',
    description: '',
})

// -----------------------------------------------------------------------------
// PRESELECCIÓN DESDE LA URL
// -----------------------------------------------------------------------------

onMounted(async () => {
    const params = new URLSearchParams(window.location.search)

    const productParam =
        params.get('producto') || params.get('product')

    const serviceParam =
        params.get('servicio') || params.get('service')

    const productId = productParam ? Number(productParam) : null
    const serviceId = serviceParam ? Number(serviceParam) : null

    if (
        productId !== null &&
        Number.isInteger(productId) &&
        productId > 0
    ) {
        const productExists = props.quoteOptions.products.some(
            product => product.id === productId,
        )

        if (productExists) {
            selectedItem.value = `product:${productId}`
        }
    } else if (
        serviceId !== null &&
        Number.isInteger(serviceId) &&
        serviceId > 0
    ) {
        const serviceExists = props.quoteOptions.services.some(
            service => service.id === serviceId,
        )

        if (serviceExists) {
            selectedItem.value = `service:${serviceId}`
        }
    }

    if (window.location.hash === '#contacto') {
        await nextTick()

        document.querySelector('#contacto')?.scrollIntoView({
            behavior: 'smooth',
            block: 'start',
        })
    }
})

// -----------------------------------------------------------------------------
// NOTIFICACIONES
// -----------------------------------------------------------------------------

function showToast(
    message: string,
    type: 'success' | 'error' = 'success',
) {
    if (toastTimeout) {
        clearTimeout(toastTimeout)
    }

    toastMessage.value = message
    toastType.value = type
    toastVisible.value = true

    toastTimeout = setTimeout(() => {
        toastVisible.value = false
    }, 5000)
}

function closeToast() {
    if (toastTimeout) {
        clearTimeout(toastTimeout)
    }

    toastVisible.value = false
}

// -----------------------------------------------------------------------------
// ENVÍO DE COTIZACIÓN
// -----------------------------------------------------------------------------

function submitQuote() {
    const form = quoteFormRef.value

    if (!form || !form.reportValidity()) {
        return
    }

    const [type, id] = selectedItem.value.split(':')
    const selectedId = Number(id)

    quoteForm.product_id = null
    quoteForm.service_id = null
    quoteForm.item_name = ''

    if (type === 'product' && selectedId) {
        const product = props.quoteOptions.products.find(
            item => item.id === selectedId,
        )

        if (!product) {
            showToast(
                'El producto seleccionado ya no está disponible.',
                'error',
            )
            return
        }

        quoteForm.product_id = product.id
        quoteForm.item_name = product.name
    } else if (type === 'service' && selectedId) {
        const service = props.quoteOptions.services.find(
            item => item.id === selectedId,
        )

        if (!service) {
            showToast(
                'El servicio seleccionado ya no está disponible.',
                'error',
            )
            return
        }

        quoteForm.service_id = service.id
        quoteForm.item_name = service.name
    } else if (selectedItem.value === 'other') {
        quoteForm.item_name = 'Otro proyecto'
    } else {
        showToast('Selecciona un producto o servicio.', 'error')
        return
    }

    quoteForm.post('/quotes', {
        preserveScroll: true,

        onSuccess: () => {
            quoteForm.reset()
            selectedItem.value = ''

            showToast(
                '¡Tu solicitud de cotización se envió correctamente! Nos pondremos en contacto contigo.',
                'success',
            )
        },

        onError: () => {
            showToast(
                'No fue posible enviar tu solicitud. Revisa los campos e inténtalo nuevamente.',
                'error',
            )
        },
    })
}

// -----------------------------------------------------------------------------
// PRODUCTOS
// -----------------------------------------------------------------------------

const products = computed(() =>
    props.products.map((product, index) => ({
        id: product.id,
        slug: product.slug,
        href: `/productos/${product.slug}`,
        number: String(index + 1).padStart(2, '0'),
        title: product.name,
        description: product.short_description || '',
        image: product.image_url,
        imageAlt: product.image_alt || product.name,
        variants: product.variants ?? [],
    })),
)

// -----------------------------------------------------------------------------
// SERVICIOS
// -----------------------------------------------------------------------------

const services = computed(() =>
    props.services.map((service, index) => ({
        id: service.id,
        slug: service.slug,
        href: `/servicios/${service.slug}`,
        number: String(index + 1).padStart(2, '0'),
        title: service.name,
        description: service.description || '',
    })),
)

// -----------------------------------------------------------------------------
// PROYECTOS
// -----------------------------------------------------------------------------

const projectItems = computed(() =>
    props.projects.map((project, index) => ({
        id: project.id,
        slug: project.slug,
        href: `/proyectos/${project.slug}`,
        number: String(index + 1).padStart(2, '0'),
        title: project.name,
        description: project.short_description || '',
        image: project.image_url,
        imageAlt: project.image_alt || project.name,
    })),
)

// -----------------------------------------------------------------------------
// GALERÍA NOSOTROS
// -----------------------------------------------------------------------------

const nosotrosGallery = computed(() =>
    (props.about?.gallery_images ?? [])
        .slice()
        .sort((a, b) => a.sort_order - b.sort_order)
        .map(image => ({
            id: image.id,
            image: image.url,
            imageAlt: image.alt || 'Galería de proyectos de Grupo Gilant',
        })),
)

// -----------------------------------------------------------------------------
// CONTACTO
// -----------------------------------------------------------------------------

const contactEmail = computed(
    () => props.contact?.email || 'info@gilant.com.mx',
)

const contactPhones = computed(() =>
    [
        props.contact?.phone_primary,
        props.contact?.phone_secondary,
        props.contact?.phone_tertiary,
    ].filter((phone): phone is string => Boolean(phone)),
)

const whatsappNumber = computed(() => {
    const number =
        props.contact?.whatsapp ||
        props.contact?.phone_primary ||
        ''

    return number.replace(/\D/g, '')
})

const whatsappUrl = computed(() => {
    const message = encodeURIComponent(
        'Hola, me gustaría solicitar información sobre sus productos y servicios.',
    )

    return whatsappNumber.value
        ? `https://wa.me/${whatsappNumber.value}?text=${message}`
        : null
})

const contactBackgroundImage = computed(() => {
    const gallery = props.about?.gallery_images ?? []

    const primaryImage = gallery.find(image => image.is_primary)

    return (
        primaryImage?.url ||
        gallery[0]?.url ||
        props.banner?.image_url ||
        ''
    )
})

function phoneUrl(phone: string) {
    return `tel:${phone.replace(/[^\d+]/g, '')}`
}
</script>

<template>
    <Head>
        <title>{{ seoTitle }}</title>

        <meta
            head-key="description"
            name="description"
            :content="seoDescription"
        />

        <link
            head-key="canonical"
            rel="canonical"
            :href="canonicalUrl"
        />

        <meta
            head-key="theme-color"
            name="theme-color"
            content="#f8f8f2"
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
            :content="ogImage"
        />

        <meta
            head-key="og:image:alt"
            property="og:image:alt"
            content="Grupo Gilant, arquitectura y construcción en madera"
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
            :content="ogImage"
        />

        <!-- DATOS ESTRUCTURADOS -->
        <component
            :is="'script'"
            type="application/ld+json"
            head-key="organization-schema"
            v-html="organizationSchemaJson"
        />
    </Head>

    <div class="gilant-site">
        <GilantHeader />

        <main>
            <!-- PORTADA -->
            <GilantHero
                :banner="props.banner"
                scroll-target="#nosotros"
                min-height="80vh"
            />

            <!-- ----------------------------------------------------------------
                 NOSOTROS
            ----------------------------------------------------------------- -->
            <ItemCards
                id="nosotros"
                variant="featured"
                eyebrow="GRUPO GILANT"
                title="Más de 50 años construyendo confianza."
                :items="nosotrosGallery"
                background="#F7F4EC"
                section-padding="lg"
                gallery-position="right"
                link-href="/nosotros"
                action-label="Conocer más"
                image-alt="Grupo Gilant, soluciones arquitectónicas en madera"
            >
                <template #intro>
                    <p class="gilant-body-copy">
                        {{
                            props.about?.history ||
                            'Desarrollamos soluciones arquitectónicas y constructivas en madera impregnada. Integramos procesamiento, secado, tratamiento, asesoría e instalación para proyectos habitacionales, industriales, turísticos, agropecuarios y de infraestructura.'
                        }}
                    </p>
                </template>
            </ItemCards>

            <!-- ----------------------------------------------------------------
                 SERVICIOS
            ----------------------------------------------------------------- -->
            <ItemCards
                id="servicios"
                variant="list"
                :items="services"
                eyebrow="SOLUCIONES GILANT"
                title="Diseño, ingeniería y construcción en madera."
                link-text="Conoce más"
                link-href="/servicios"
                empty-text="Estamos preparando nuestro catálogo de servicios."
                background="#FFFFFF"
                section-padding="lg"
            >
                <template #intro>
                    <p class="gilant-services-lead">
                        Acompañamos cada proyecto desde la conceptualización y la
                        ingeniería hasta la fabricación, suministro e instalación
                        de soluciones constructivas en madera.
                    </p>
                </template>

                <template #footer>
                    ¿Tienes un proyecto especial?
                </template>
            </ItemCards>

            <!-- ----------------------------------------------------------------
                 PROYECTOS
            ----------------------------------------------------------------- -->
            <ItemCards
                id="proyectos"
                variant="featured"
                eyebrow="NUESTRO PORTAFOLIO"
                title="Nuestros proyectos"
                :items="projectItems"
                action-label="Conocer más"
                empty-text="Estamos preparando nuestra galería de proyectos."
                background="#F7F4EC"
                section-padding="lg"
                gallery-position="right"
            />

            <!-- ----------------------------------------------------------------
                 CLIENTES
            ----------------------------------------------------------------- -->
            <section
                id="clientes"
                class="gilant-clients"
            >
                <div class="gilant-container">
                    <div class="gilant-clients-heading">
                        <p class="gilant-eyebrow">
                            RELACIONES QUE PERDURAN
                        </p>

                        <h2>
                            Relaciones sólidas. Proyectos extraordinarios.
                        </h2>
                    </div>
                </div>

                <div
                    v-if="props.clients.length"
                    class="gilant-clients-grid"
                >
                    <div
                        v-for="client in props.clients"
                        :key="client.id"
                        class="gilant-client-logo"
                    >
                        <img
                            v-if="client.logo_url"
                            :src="client.logo_url"
                            :alt="`Logotipo de ${client.name}`"
                            loading="lazy"
                        />

                        <span
                            v-else
                            class="gilant-client-placeholder"
                            aria-hidden="true"
                        >
                            <TreeDeciduous
                                :size="23"
                                stroke-width="1.2"
                            />
                        </span>
                    </div>
                </div>

                <div
                    v-else
                    class="gilant-clients-empty"
                >
                    <TreeDeciduous
                        :size="34"
                        stroke-width="1.3"
                    />

                    <p>
                        Próximamente compartiremos más proyectos y colaboraciones.
                    </p>
                </div>
            </section>

            <!-- ----------------------------------------------------------------
                 CONTACTO
                 Fondo fotográfico / Panel centrado
            ----------------------------------------------------------------- -->
            <section
                id="contacto"
                class="gilant-contact"
                :style="{
                    backgroundImage: contactBackgroundImage
                        ? `linear-gradient(120deg, rgba(7, 28, 20, .78), rgba(7, 28, 20, .62)), url('${contactBackgroundImage}')`
                        : 'linear-gradient(120deg, #123D2D, #0B2B20)',
                }"
            >
                <div class="gilant-contact-panel">

                    <!-- ENCABEZADO -->
                    <div class="gilant-contact-heading">
                        <p class="gilant-contact-eyebrow">
                            GRUPO GILANT · CONTACTO
                        </p>

                        <h2>
                            Construyamos algo extraordinario.
                        </h2>

                        <p>
                            Cuéntanos sobre tu proyecto. Nuestro equipo está listo
                            para ayudarte a encontrar la solución adecuada.
                        </p>
                    </div>

                    <!-- DATOS DE CONTACTO -->
                    <div class="gilant-contact-information">

                        <!-- CORREO -->
                        <a
                            v-if="contactEmail"
                            class="gilant-contact-information-item"
                            :href="`mailto:${contactEmail}`"
                        >
                            <span class="gilant-contact-information-icon">
                                <Mail :size="20" />
                            </span>

                            <span class="gilant-contact-information-text">
                                <small>Correo electrónico</small>
                                <strong>{{ contactEmail }}</strong>
                            </span>

                            <ArrowUpRight
                                :size="17"
                                class="gilant-contact-information-arrow"
                            />
                        </a>

                        <!-- TELÉFONOS -->
                        <div
                            v-if="contactPhones.length"
                            class="gilant-contact-information-item"
                        >
                            <span class="gilant-contact-information-icon">
                                <Phone :size="20" />
                            </span>

                            <span class="gilant-contact-information-text">
                                <small>Atención comercial</small>

                                <a
                                    v-for="(phone, index) in contactPhones"
                                    :key="index"
                                    :href="phoneUrl(phone)"
                                >
                                    {{ phone }}
                                </a>
                            </span>
                        </div>

                        <!-- DIRECCIÓN Y HORARIOS -->
                        <div
                            v-if="
                                props.contact?.address ||
                                props.contact?.schedule_weekdays ||
                                props.contact?.schedule_saturday
                            "
                            class="gilant-contact-information-item"
                        >
                            <span class="gilant-contact-information-icon">
                                <MapPin :size="20" />
                            </span>

                            <span class="gilant-contact-information-text">
                                <small>Visítanos</small>

                                <strong v-if="props.contact?.address">
                                    {{ props.contact.address }}
                                </strong>

                                <span v-if="props.contact?.schedule_weekdays">
                                    Lun. a vie. {{ props.contact.schedule_weekdays }}
                                </span>

                                <span v-if="props.contact?.schedule_saturday">
                                    Sábado {{ props.contact.schedule_saturday }}
                                </span>
                            </span>
                        </div>
                    </div>

                    <!-- REDES SOCIALES -->
                    <div
                        v-if="
                            props.contact?.facebook_url ||
                            props.contact?.instagram_url
                        "
                        class="gilant-contact-social"
                    >
                        <span class="gilant-contact-social-label">
                            SÍGUENOS Y CONOCE NUESTRO TRABAJO
                        </span>

                        <div class="gilant-contact-social-links">
                            <a
                                v-if="props.contact?.facebook_url"
                                :href="props.contact.facebook_url"
                                target="_blank"
                                rel="noopener noreferrer"
                                aria-label="Visitar Facebook de Grupo Gilant"
                            >
                                <Facebook :size="19" />
                                <span>Facebook</span>
                                <ArrowUpRight :size="15" />
                            </a>

                            <a
                                v-if="props.contact?.instagram_url"
                                :href="props.contact.instagram_url"
                                target="_blank"
                                rel="noopener noreferrer"
                                aria-label="Visitar Instagram de Grupo Gilant"
                            >
                                <Instagram :size="19" />
                                <span>Instagram</span>
                                <ArrowUpRight :size="15" />
                            </a>
                        </div>
                    </div>

                    <!-- DIVISOR -->
                    <div class="gilant-contact-divider"></div>

                    <!-- FORMULARIO -->
                    <div class="gilant-contact-form-heading">
                        <span>SOLICITA UNA COTIZACIÓN</span>

                        <h3>
                            Cuéntanos qué tienes en mente.
                        </h3>

                        <p>
                            Completa el formulario y nos pondremos en contacto contigo.
                        </p>
                    </div>

                    <form
                        id="gilant-quote-form"
                        ref="quoteFormRef"
                        class="gilant-quote-form"
                        @submit.prevent="submitQuote"
                    >
                        <div class="gilant-form-row">
                            <label>
                                Nombre completo

                                <input
                                    v-model="quoteForm.name"
                                    name="name"
                                    autocomplete="name"
                                    placeholder="¿Cómo te llamas?"
                                    required
                                />

                                <small
                                    v-if="quoteForm.errors.name"
                                    class="gilant-form-error"
                                >
                                    {{ quoteForm.errors.name }}
                                </small>
                            </label>

                            <label>
                                Empresa

                                <input
                                    v-model="quoteForm.company"
                                    name="company"
                                    autocomplete="organization"
                                    placeholder="Nombre de tu empresa"
                                />

                                <small
                                    v-if="quoteForm.errors.company"
                                    class="gilant-form-error"
                                >
                                    {{ quoteForm.errors.company }}
                                </small>
                            </label>
                        </div>

                        <div class="gilant-form-row">
                            <label>
                                Correo electrónico

                                <input
                                    v-model="quoteForm.email"
                                    type="email"
                                    name="email"
                                    autocomplete="email"
                                    placeholder="nombre@empresa.com"
                                    required
                                />

                                <small
                                    v-if="quoteForm.errors.email"
                                    class="gilant-form-error"
                                >
                                    {{ quoteForm.errors.email }}
                                </small>
                            </label>

                            <label>
                                Teléfono

                                <input
                                    v-model="quoteForm.phone"
                                    type="tel"
                                    name="phone"
                                    autocomplete="tel"
                                    placeholder="+52"
                                    required
                                />

                                <small
                                    v-if="quoteForm.errors.phone"
                                    class="gilant-form-error"
                                >
                                    {{ quoteForm.errors.phone }}
                                </small>
                            </label>
                        </div>

                        <label>
                            ¿Qué solución necesitas?

                            <span class="gilant-select-wrap">
                                <select
                                    v-model="selectedItem"
                                    name="interest"
                                    required
                                >
                                    <option value="" disabled>
                                        Selecciona una opción
                                    </option>

                                    <optgroup
                                        v-if="props.quoteOptions.products.length"
                                        label="Productos"
                                    >
                                        <option
                                            v-for="product in props.quoteOptions.products"
                                            :key="`product-${product.id}`"
                                            :value="`product:${product.id}`"
                                        >
                                            {{ product.name }}
                                        </option>
                                    </optgroup>

                                    <optgroup
                                        v-if="props.quoteOptions.services.length"
                                        label="Servicios"
                                    >
                                        <option
                                            v-for="service in props.quoteOptions.services"
                                            :key="`service-${service.id}`"
                                            :value="`service:${service.id}`"
                                        >
                                            {{ service.name }}
                                        </option>
                                    </optgroup>

                                    <option value="other">
                                        Otro proyecto
                                    </option>
                                </select>

                                <ChevronDown :size="16" />
                            </span>

                            <small
                                v-if="quoteForm.errors.product_id"
                                class="gilant-form-error"
                            >
                                {{ quoteForm.errors.product_id }}
                            </small>

                            <small
                                v-if="quoteForm.errors.service_id"
                                class="gilant-form-error"
                            >
                                {{ quoteForm.errors.service_id }}
                            </small>

                            <small
                                v-if="quoteForm.errors.item_name"
                                class="gilant-form-error"
                            >
                                {{ quoteForm.errors.item_name }}
                            </small>
                        </label>

                        <label>
                            Cuéntanos un poco más

                            <textarea
                                v-model="quoteForm.description"
                                name="message"
                                rows="4"
                                placeholder="Producto, medidas, volumen, ubicación o fecha estimada…"
                            />

                            <small
                                v-if="quoteForm.errors.description"
                                class="gilant-form-error"
                            >
                                {{ quoteForm.errors.description }}
                            </small>
                        </label>

                        <button
                            class="gilant-contact-submit"
                            type="submit"
                            :disabled="quoteForm.processing"
                        >
                            {{
                                quoteForm.processing
                                    ? 'Enviando solicitud...'
                                    : 'Enviar solicitud'
                            }}

                            <ArrowRight
                                v-if="!quoteForm.processing"
                                :size="18"
                            />
                        </button>
                    </form>
                </div>
            </section>
        </main>

        <GilantFooter />

        <!-- TOAST DE NOTIFICACIÓN -->
        <Transition name="gilant-toast">
            <div
                v-if="toastVisible"
                class="gilant-toast"
                :class="`gilant-toast-${toastType}`"
                role="status"
                aria-live="polite"
            >
                <span class="gilant-toast-icon">
                    <CheckCircle2
                        v-if="toastType === 'success'"
                        :size="22"
                    />

                    <X
                        v-else
                        :size="22"
                    />
                </span>

                <div class="gilant-toast-content">
                    <strong>
                        {{
                            toastType === 'success'
                                ? '¡Solicitud enviada!'
                                : 'No se pudo enviar'
                        }}
                    </strong>

                    <p>{{ toastMessage }}</p>
                </div>

                <button
                    type="button"
                    class="gilant-toast-close"
                    aria-label="Cerrar notificación"
                    @click="closeToast"
                >
                    <X :size="17" />
                </button>
            </div>
        </Transition>

        <!-- WHATSAPP -->
        <a
            v-if="whatsappUrl"
            class="gilant-whatsapp"
            :href="whatsappUrl"
            target="_blank"
            rel="noopener noreferrer"
            aria-label="Contactar a Grupo Gilant por WhatsApp"
            title="Escríbenos por WhatsApp"
        >
            <svg
                viewBox="0 0 32 32"
                width="29"
                height="29"
                fill="currentColor"
                aria-hidden="true"
            >
                <path
                    d="M16.04 3C8.88 3 3.05 8.81 3.05 15.95c0 2.28.6 4.5 1.73 6.46L3 29l6.78-1.77a13.1 13.1 0 0 0 6.25 1.59h.01c7.15 0 12.98-5.81 12.98-12.96A12.9 12.9 0 0 0 16.04 3Zm0 23.64c-1.95 0-3.87-.53-5.54-1.53l-.4-.24-4.02 1.05 1.07-3.91-.26-.41a10.63 10.63 0 0 1-1.64-5.65c0-5.95 4.84-10.79 10.8-10.79 2.88 0 5.59 1.12 7.62 3.16a10.72 10.72 0 0 1 3.16 7.63c0 5.95-4.84 10.79-10.79 10.79Zm5.92-8.08c-.32-.16-1.9-.94-2.2-1.05-.3-.11-.51-.16-.72.16-.21.32-.83 1.05-1.02 1.26-.19.21-.38.24-.7.08-.32-.16-1.35-.5-2.57-1.59-.95-.85-1.59-1.9-1.78-2.22-.19-.32-.02-.49.14-.65.14-.14.32-.38.48-.56.16-.19.21-.32.32-.54.11-.21.05-.4-.03-.56-.08-.16-.72-1.74-.99-2.38-.26-.62-.52-.53-.72-.54h-.62c-.21 0-.56.08-.86.4-.3.32-1.13 1.1-1.13 2.68s1.16 3.1 1.32 3.32c.21.32 2.28 3.48 5.53 4.88.77.33 1.37.52 1.84.66.77.24 1.47.21 2.02.13.62-.09 1.9-.78 2.17-1.53.27-.75.19-1.39.19-1.53-.08-.14-.3-.22-.62-.38Z"
                />
            </svg>

            <span class="gilant-whatsapp-tooltip">
                Hablemos de tu proyecto.
            </span>
        </a>
    </div>
</template>

<style scoped>
/* =========================================================
   LAYOUT GLOBAL DE LA LANDING
========================================================= */

.gilant-site {
    width: 100%;
    min-width: 0;
    overflow-x: clip;
}

.gilant-site main {
    width: 100%;
    min-width: 0;
}

.gilant-site main > * {
    width: 100%;
    max-width: none;
}

.gilant-site :deep(.gilant-container) {
    width: min(88%, 1440px);
    max-width: 1440px;
    margin-inline: auto;
}

.gilant-site :deep(.gilant-section) {
    position: relative;
    width: 100%;
    max-width: none;
}

@media (max-width: 850px) {
    .gilant-site :deep(.gilant-container) {
        width: 92%;
    }
}

@media (max-width: 480px) {
    .gilant-site :deep(.gilant-container) {
        width: 90%;
    }
}

/* =========================================================
   CLIENTES
   Full width / 4 columnas iguales
========================================================= */

.gilant-clients {
    width: 100%;
    padding: 90px 0 0;
    overflow: hidden;
    background: #fffefa;
}

.gilant-clients-heading {
    max-width: 760px;
    margin: 0 auto;
    padding-bottom: 55px;
    text-align: center;
}

.gilant-clients-heading .gilant-eyebrow {
    margin-bottom: 12px;
}

.gilant-clients-heading h2 {
    margin: 0;
    color: #25352d;
    font-family: var(--gilant-font-heading);
    font-size: clamp(1.8rem, 3vw, 2.5rem);
    font-weight: 600;
    line-height: 1.2;
}

.gilant-clients-grid {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    width: 100%;
    border-top: 1px solid #e8e7df;
    border-bottom: 1px solid #e8e7df;
}

.gilant-client-logo {
    display: flex;
    align-items: center;
    justify-content: center;
    min-width: 0;
    min-height: 150px;
    padding: 24px 32px;
    border-right: 1px solid #e8e7df;
    transition: background 250ms ease;
}

.gilant-client-logo:last-child {
    border-right: none;
}

.gilant-client-logo:hover {
    background: var(--gilant-cream);
}

.gilant-client-logo img {
    display: block;
    width: 100%;
    max-width: 190px;
    height: 72px;
    object-fit: contain;
    object-position: center;
    filter: grayscale(100%);
    opacity: 0.75;
    transition:
        filter 250ms ease,
        opacity 250ms ease,
        transform 250ms ease;
}

.gilant-client-logo:hover img {
    filter: grayscale(0);
    opacity: 1;
    transform: scale(1.04);
}

.gilant-clients-empty {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 12px;
    padding: 0 24px 90px;
    color: var(--gilant-olive);
    text-align: center;
}

.gilant-clients-empty p {
    margin: 0;
    color: var(--gilant-text-light);
}

.gilant-client-placeholder {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 58px;
    height: 58px;
    color: var(--gilant-olive);
    border: 1px solid #e8e7df;
    border-radius: 50%;
}

@media (max-width: 900px) {
    .gilant-clients-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .gilant-client-logo {
        min-height: 130px;
        border-bottom: 1px solid #e8e7df;
    }

    .gilant-client-logo:nth-child(2n) {
        border-right: none;
    }

    .gilant-client-logo:nth-last-child(-n + 2) {
        border-bottom: none;
    }
}

@media (max-width: 560px) {
    .gilant-clients {
        padding-top: 65px;
    }

    .gilant-clients-heading {
        padding-bottom: 35px;
    }

    .gilant-clients-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .gilant-client-logo {
        min-height: 110px;
        padding: 18px;
    }

    .gilant-client-logo img {
        max-width: 145px;
        height: 52px;
    }
}

@media (prefers-reduced-motion: reduce) {
    .gilant-client-logo,
    .gilant-client-logo img {
        transition: none;
    }
}

/* =========================================================
   CONTACTO
   Fondo fotográfico full width
   Panel central con overlay Gilant
========================================================= */

.gilant-contact {
    position: relative;
    display: flex;
    justify-content: center;
    align-items: center;
    width: 100%;
    min-height: 900px;
    padding: 100px 24px;
    overflow: hidden;
    background-color: var(--gilant-forest);
    background-position: center;
    background-repeat: no-repeat;
    background-size: cover;
}

/* Panel principal */
.gilant-contact-panel {
    position: relative;
    z-index: 1;
    width: min(100%, 960px);
    padding: clamp(32px, 5vw, 64px);
    overflow: hidden;
    color: #fff;
    background: rgba(11, 43, 32, 0.88);
    border: 1px solid rgba(255, 255, 255, 0.17);
    border-radius: 5px;
    box-shadow: 0 25px 80px rgba(0, 0, 0, 0.25);
    backdrop-filter: blur(13px);
    -webkit-backdrop-filter: blur(13px);
}

/* Encabezado */
.gilant-contact-heading {
    max-width: 700px;
    margin: 0 auto 38px;
    text-align: center;
}

.gilant-contact-eyebrow {
    margin: 0 0 16px;
    color: var(--gilant-gold);
    font-size: 0.68rem;
    font-weight: 800;
    letter-spacing: 0.22em;
}

.gilant-contact-heading h2 {
    margin: 0;
    color: #fff;
    font-family: var(--gilant-font-heading);
    font-size: clamp(2rem, 4vw, 3.1rem);
    font-weight: 600;
    line-height: 1.12;
    letter-spacing: -0.045em;
}

.gilant-contact-heading > p:last-child {
    max-width: 560px;
    margin: 18px auto 0;
    color: rgba(255, 255, 255, 0.76);
    font-size: 0.94rem;
    line-height: 1.7;
}

/* Datos de contacto */
.gilant-contact-information {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 14px;
    width: 100%;
}

.gilant-contact-information-item {
    display: flex;
    align-items: flex-start;
    gap: 13px;
    min-width: 0;
    padding: 19px 15px;
    color: #fff;
    text-decoration: none;
    background: rgba(255, 255, 255, 0.055);
    border: 1px solid rgba(255, 255, 255, 0.12);
    border-radius: 3px;
    transition:
        background 220ms ease,
        border-color 220ms ease;
}

a.gilant-contact-information-item:hover {
    color: #fff;
    background: rgba(255, 255, 255, 0.11);
    border-color: rgba(196, 154, 90, 0.55);
}

.gilant-contact-information-icon {
    display: flex;
    flex: 0 0 38px;
    align-items: center;
    justify-content: center;
    width: 38px;
    height: 38px;
    color: var(--gilant-gold);
    background: rgba(196, 154, 90, 0.12);
    border-radius: 50%;
}

.gilant-contact-information-text {
    display: flex;
    flex: 1;
    flex-direction: column;
    gap: 5px;
    min-width: 0;
    padding-top: 2px;
}

.gilant-contact-information-text small {
    color: rgba(255, 255, 255, 0.58);
    font-size: 0.65rem;
    font-weight: 700;
    letter-spacing: 0.09em;
    text-transform: uppercase;
}

.gilant-contact-information-text strong,
.gilant-contact-information-text a,
.gilant-contact-information-text > span {
    color: #fff;
    font-size: 0.79rem;
    font-weight: 500;
    line-height: 1.5;
    overflow-wrap: anywhere;
}

.gilant-contact-information-text a {
    text-decoration: none;
    transition: color 180ms ease;
}

.gilant-contact-information-text a:hover {
    color: #d8b77f;
}

.gilant-contact-information-arrow {
    flex: 0 0 auto;
    margin-top: 2px;
    color: rgba(255, 255, 255, 0.5);
}

/* Redes sociales */
.gilant-contact-social {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 20px;
    margin-top: 22px;
}

.gilant-contact-social-label {
    color: rgba(255, 255, 255, 0.55);
    font-size: 0.64rem;
    font-weight: 700;
    letter-spacing: 0.12em;
}

.gilant-contact-social-links {
    display: flex;
    align-items: center;
    gap: 10px;
}

.gilant-contact-social-links a {
    display: inline-flex;
    align-items: center;
    gap: 9px;
    padding: 9px 13px;
    color: #fff;
    font-size: 0.75rem;
    text-decoration: none;
    background: rgba(255, 255, 255, 0.07);
    border: 1px solid rgba(255, 255, 255, 0.13);
    border-radius: 3px;
    transition:
        color 200ms ease,
        background 200ms ease,
        border-color 200ms ease;
}

.gilant-contact-social-links a:hover {
    color: #fff;
    background: rgba(196, 154, 90, 0.2);
    border-color: var(--gilant-gold);
}

/* Separador */
.gilant-contact-divider {
    width: 100%;
    height: 1px;
    margin: 32px 0;
    background: rgba(255, 255, 255, 0.16);
}

/* Encabezado del formulario */
.gilant-contact-form-heading {
    margin-bottom: 25px;
}

.gilant-contact-form-heading > span {
    color: var(--gilant-gold);
    font-size: 0.65rem;
    font-weight: 800;
    letter-spacing: 0.18em;
}

.gilant-contact-form-heading h3 {
    margin: 8px 0 5px;
    color: #fff;
    font-family: var(--gilant-font-heading);
    font-size: clamp(1.35rem, 2.5vw, 1.8rem);
    font-weight: 600;
    line-height: 1.25;
}

.gilant-contact-form-heading p {
    margin: 0;
    color: rgba(255, 255, 255, 0.65);
    font-size: 0.83rem;
    line-height: 1.6;
}

/* Formulario */
.gilant-quote-form {
    display: flex;
    flex-direction: column;
    gap: 17px;
    width: 100%;
}

.gilant-form-row {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 17px;
}

.gilant-quote-form label {
    display: flex;
    flex-direction: column;
    gap: 8px;
    min-width: 0;
    color: rgba(255, 255, 255, 0.86);
    font-size: 0.74rem;
    font-weight: 600;
}

.gilant-quote-form input,
.gilant-quote-form select,
.gilant-quote-form textarea {
    width: 100%;
    min-width: 0;
    padding: 13px 15px;
    color: #25352d;
    font-family: inherit;
    font-size: 0.82rem;
    font-weight: 400;
    line-height: 1.5;
    background: #fffefa;
    border: 1px solid rgba(255, 255, 255, 0.25);
    border-radius: 3px;
    outline: none;
    transition:
        border-color 180ms ease,
        box-shadow 180ms ease,
        background 180ms ease;
}

.gilant-quote-form input,
.gilant-quote-form select {
    min-height: 47px;
}

.gilant-quote-form input::placeholder,
.gilant-quote-form textarea::placeholder {
    color: #92978f;
}

.gilant-quote-form input:focus,
.gilant-quote-form select:focus,
.gilant-quote-form textarea:focus {
    background: #fff;
    border-color: var(--gilant-gold);
    box-shadow: 0 0 0 3px rgba(196, 154, 90, 0.18);
}

.gilant-quote-form textarea {
    min-height: 105px;
    resize: vertical;
}

.gilant-select-wrap {
    position: relative;
    display: block;
    width: 100%;
}

.gilant-select-wrap select {
    padding-right: 42px;
    appearance: none;
    -webkit-appearance: none;
}

.gilant-select-wrap > svg {
    position: absolute;
    top: 50%;
    right: 15px;
    color: var(--gilant-olive);
    pointer-events: none;
    transform: translateY(-50%);
}

.gilant-form-error {
    color: #ffb7a9;
    font-size: 0.72rem;
    font-weight: 500;
}

/* Botón */
.gilant-contact-submit {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 13px;
    align-self: flex-start;
    min-height: 50px;
    margin-top: 5px;
    padding: 0 27px;
    color: #fff;
    font-family: inherit;
    font-size: 0.75rem;
    font-weight: 800;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    background: var(--gilant-gold);
    border: 1px solid var(--gilant-gold);
    border-radius: 3px;
    cursor: pointer;
    transition:
        background 200ms ease,
        border-color 200ms ease,
        transform 200ms ease;
}

.gilant-contact-submit:hover:not(:disabled) {
    background: #a98145;
    border-color: #a98145;
    transform: translateY(-2px);
}

.gilant-contact-submit:disabled {
    cursor: not-allowed;
    opacity: 0.65;
}

/* Tablet */
@media (max-width: 850px) {
    .gilant-contact {
        min-height: auto;
        padding: 75px 22px;
    }

    .gilant-contact-panel {
        width: min(100%, 720px);
        padding: 38px;
    }

    .gilant-contact-information {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .gilant-contact-information-item:last-child:nth-child(odd) {
        grid-column: 1 / -1;
    }
}

/* Móvil */
@media (max-width: 600px) {
    .gilant-contact {
        padding: 48px 15px;
        background-position: center;
    }

    .gilant-contact-panel {
        padding: 29px 20px;
        border-radius: 3px;
    }

    .gilant-contact-heading {
        margin-bottom: 27px;
    }

    .gilant-contact-heading h2 {
        font-size: clamp(1.8rem, 8vw, 2.35rem);
    }

    .gilant-contact-heading > p:last-child {
        font-size: 0.84rem;
    }

    .gilant-contact-information {
        grid-template-columns: minmax(0, 1fr);
        gap: 9px;
    }

    .gilant-contact-information-item,
    .gilant-contact-information-item:last-child:nth-child(odd) {
        grid-column: auto;
        padding: 15px;
    }

    .gilant-contact-social {
        align-items: flex-start;
        flex-direction: column;
        gap: 13px;
    }

    .gilant-contact-social-links {
        flex-wrap: wrap;
    }

    .gilant-contact-divider {
        margin: 25px 0;
    }

    .gilant-form-row {
        grid-template-columns: minmax(0, 1fr);
        gap: 17px;
    }

    .gilant-contact-submit {
        width: 100%;
        align-self: stretch;
    }
}

@media (prefers-reduced-motion: reduce) {
    .gilant-contact-information-item,
    .gilant-contact-information-text a,
    .gilant-contact-social-links a,
    .gilant-contact-submit {
        transition: none;
    }
}

/* =========================================================
   WHATSAPP FLOTANTE — GILANT
========================================================= */

.gilant-whatsapp {
    position: fixed;
    right: 28px;
    bottom: 28px;
    z-index: 999;
    display: flex;
    align-items: center;
    justify-content: center;
    width: 62px;
    height: 62px;
    border: 1px solid rgb(255 255 255 / 25%);
    border-radius: 50%;
    color: #fff;
    background: var(--gilant-forest);
    box-shadow:
        0 8px 25px rgb(11 43 32 / 28%),
        inset 0 1px 0 rgb(255 255 255 / 16%);
    text-decoration: none;
    isolation: isolate;
    transition:
        transform 250ms ease,
        background 250ms ease,
        box-shadow 250ms ease;
}

.gilant-whatsapp:hover {
    transform: translateY(-4px);
    color: #fff;
    background: #1b513b;
    box-shadow: 0 12px 30px rgb(11 43 32 / 35%);
}

.gilant-whatsapp svg {
    position: relative;
    z-index: 2;
    display: block;
    width: 30px;
    height: 30px;
    flex-shrink: 0;
}

/* Pulso exterior dorado */

.gilant-whatsapp::before {
    position: absolute;
    z-index: -1;
    inset: -5px;
    border: 1px solid rgb(196 154 90 / 55%);
    border-radius: 50%;
    content: '';
    animation: gilant-whatsapp-pulse 2.8s ease-out infinite;
}

/* Tooltip */

.gilant-whatsapp-tooltip {
    position: absolute;
    right: 76px;
    width: max-content;
    max-width: 230px;
    padding: 13px 17px;
    border: 1px solid rgb(196 154 90 / 30%);
    border-radius: 3px;
    color: var(--gilant-cream);
    background: var(--gilant-forest-dark);
    box-shadow: 0 8px 25px rgb(0 0 0 / 16%);
    font-family: var(--gilant-font-heading);
    font-size: 0.78rem;
    font-weight: 600;
    white-space: nowrap;
    opacity: 0;
    visibility: hidden;
    transform: translateX(8px);
    transition:
        opacity 200ms ease,
        transform 200ms ease,
        visibility 200ms ease;
    pointer-events: none;
}

.gilant-whatsapp-tooltip::after {
    position: absolute;
    top: 50%;
    right: -6px;
    width: 10px;
    height: 10px;
    border-top: 1px solid rgb(196 154 90 / 30%);
    border-right: 1px solid rgb(196 154 90 / 30%);
    background: var(--gilant-forest-dark);
    content: '';
    transform: translateY(-50%) rotate(45deg);
}

.gilant-whatsapp:hover .gilant-whatsapp-tooltip,
.gilant-whatsapp:focus-visible .gilant-whatsapp-tooltip {
    opacity: 1;
    visibility: visible;
    transform: translateX(0);
}

/* Animación */

@keyframes gilant-whatsapp-pulse {
    0% {
        opacity: 0.65;
        transform: scale(0.92);
    }

    75%,
    100% {
        opacity: 0;
        transform: scale(1.35);
    }
}

/* Tablet y móvil */

@media (max-width: 600px) {
    .gilant-whatsapp {
        right: 18px;
        bottom: 18px;
        width: 55px;
        height: 55px;
    }

    .gilant-whatsapp svg {
        width: 27px;
        height: 27px;
    }

    .gilant-whatsapp-tooltip {
        right: 67px;
        font-size: 0.72rem;
    }
}

/* Accesibilidad */

@media (prefers-reduced-motion: reduce) {
    .gilant-whatsapp,
    .gilant-whatsapp-tooltip {
        transition: none;
    }

    .gilant-whatsapp::before {
        animation: none;
    }
}

</style>
