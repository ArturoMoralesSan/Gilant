
<script setup lang="ts">
import { ref, onMounted, onUnmounted, nextTick } from 'vue'
import { Menu, X } from 'lucide-vue-next'

const menuOpen = ref(false)
const activeLink = ref('')
const isScrolled = ref(false)

const links = [
    { label: 'Inicio', href: '/', type: 'page' },
    { label: 'Nosotros', href: '/nosotros', type: 'page' },
    { label: 'Servicios', href: '#servicios', type: 'anchor' },
    { label: 'Proyectos', href: '#proyectos', type: 'anchor' },
    { label: 'Clientes', href: '#clientes', type: 'anchor' },
    { label: 'Contacto', href: '#contacto', type: 'anchor' },
]

let sectionObserver: IntersectionObserver | null = null

function handleScroll() {
    isScrolled.value = window.scrollY > 40
}

function closeMenu() {
    menuOpen.value = false
}

function getCurrentPage() {
    return window.location.pathname.replace(/\/$/, '') || '/'
}

function isActive(link: typeof links[number]) {
    const currentPath = getCurrentPage()

    if (link.type === 'page') {
        return currentPath === link.href
    }

    if (currentPath !== '/') {
        return false
    }

    return activeLink.value === link.href
}

function navigateToSection(hash: string) {
    closeMenu()

    if (window.location.pathname !== '/') {
        window.location.href = `/${hash}`
        return
    }

    const target = document.querySelector(hash)

    if (target) {
        activeLink.value = hash
        history.pushState(null, '', hash)

        target.scrollIntoView({
            behavior: 'smooth',
            block: 'start',
        })

        return
    }

    window.location.href = `/${hash}`
}

function navigateToPage(path: string) {
    closeMenu()

    if (getCurrentPage() === path) return

    window.location.href = path
}

async function scrollToInitialHash() {
    if (window.location.pathname !== '/') return

    const hash = window.location.hash

    if (!hash) {
        activeLink.value = '#inicio'
        return
    }

    activeLink.value = hash

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

function setupSectionObserver() {
    if (window.location.pathname !== '/') return

    sectionObserver?.disconnect()

    const sections = [
        '#inicio',
        '#especialidades',
        '#proyectos',
        '#clientes',
        '#contacto',
    ]

    sectionObserver = new IntersectionObserver(
        (entries) => {
            const visibleSections = entries
                .filter((entry) => entry.isIntersecting)
                .sort(
                    (a, b) =>
                        b.intersectionRatio - a.intersectionRatio,
                )

            if (visibleSections.length > 0) {
                activeLink.value = `#${visibleSections[0].target.id}`
            }
        },
        {
            rootMargin: '-25% 0px -60% 0px',
            threshold: [0, 0.1, 0.25, 0.5],
        },
    )

    sections.forEach((selector) => {
        const section = document.querySelector(selector)

        if (section) {
            sectionObserver?.observe(section)
        }
    })
}

onMounted(async () => {
    window.addEventListener('scroll', handleScroll, { passive: true })
    handleScroll()

    await scrollToInitialHash()
    setupSectionObserver()
})

onUnmounted(() => {
    window.removeEventListener('scroll', handleScroll)
    closeMenu()
    sectionObserver?.disconnect()
})
</script>

<template>
    <header
        id="inicio"
        class="gilant-header"
        :class="{
            'is-scrolled': isScrolled,
            'menu-is-open': menuOpen,
        }"
    >
        <div class="gilant-container gilant-header-inner">

            <!-- LOGO GILANT -->
            <a
                class="gilant-brand"
                href="/#inicio"
                aria-label="Grupo Gilant, inicio"
                @click.prevent="navigateToSection('#inicio')"
            >
                <span class="gilant-brand-text">
                    GRUPO GILANT
                </span>
            </a>

            <!-- MENÚ MÓVIL -->
            <button
                class="gilant-menu-toggle"
                type="button"
                :aria-expanded="menuOpen"
                aria-label="Abrir o cerrar menú"
                @click="menuOpen = !menuOpen"
            >
                <X v-if="menuOpen" :size="21" />
                <Menu v-else :size="21" />
            </button>

            <!-- NAVEGACIÓN -->
            <nav
                class="gilant-nav"
                :class="{ 'is-open': menuOpen }"
                aria-label="Navegación principal"
            >
                <template v-for="link in links" :key="link.href">
                    <a
                        v-if="link.type === 'page'"
                        :href="link.href"
                        :class="{ 'is-active': isActive(link) }"
                        :aria-current="isActive(link) ? 'page' : undefined"
                        @click.prevent="navigateToPage(link.href)"
                    >
                        {{ link.label }}
                    </a>

                    <a
                        v-else
                        :href="`/${link.href}`"
                        :class="{ 'is-active': isActive(link) }"
                        :aria-current="isActive(link) ? 'location' : undefined"
                        @click.prevent="navigateToSection(link.href)"
                    >
                        {{ link.label }}
                    </a>
                </template>
            </nav>
        </div>
    </header>
</template>

<style scoped>
.gilant-header-inner {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: clamp(35px, 5vw, 75px);
}

.gilant-brand {
    display: inline-flex;
    flex: 0 0 auto;
    align-items: center;
    text-decoration: none;
}

.gilant-brand-text {
    color: #fffefa;
    font-family: Manrope, sans-serif;
    font-size: 19px;
    font-weight: 800;
    letter-spacing: .12em;
    white-space: nowrap;
}

.gilant-nav {
    display: flex;
    align-items: center;
    justify-content: flex-start;
    gap: clamp(17px, 2.1vw, 30px);
}

@media (max-width: 900px) {
    .gilant-header-inner {
        justify-content: space-between;
        gap: 20px;
    }

    .gilant-nav {
        justify-content: flex-start;
    }
}

@media (max-width: 600px) {
    .gilant-brand-text {
        font-size: 17px;
    }
}
</style>
