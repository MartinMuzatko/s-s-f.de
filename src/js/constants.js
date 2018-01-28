import wNumb from 'wnumb'

export const GOOGLE_MAPS_API = 'AIzaSyBHi3lZRAAQxCSof6j5lYB775VIIDWbrck'
export const moneyFormat = wNumb({
    mark: ',',
    thousand: '.',
    suffix: ' €',
    decimals : 2
})

export const DEFAULT_SPONSORLEVELS = [
    {
        name: 'Kein Sponsor',
        minPrice: 0,
        summary: '',
        items: new Set,
    },
    {
        name: 'Fennek',
        minPrice: 5,
        summary: 'Danke für deine Unterstützung! Dich erwartet ein kleines Dankeschön',
        items: new Set,
    },
    {
        name: 'Wolf',
        minPrice: 10,
        summary: '',
        items: new Set,
    },
    {
        name: 'Tiger',
        minPrice: 20,
        summary: 'Danke für deine Unterstützung! Dich erwartet ein kleines Dankeschön',
        items: new Set,
    }
]

export const DEFAULT_ITEMS = [
    {
        name: 'Badge',
        sellPrice: 2,
        buyPrice: 2,
        variants: [],
        variantsActive: false,
        included: false
    },
    {
        name: 'T-Shirt',
        sellPrice: 16,
        buyPrice: 16,
        variants: ['S','M','L','XL','XXL','3XL'],
        variantsActive: true,
        included: false
    },
    {
        name: 'Early Bird',
        sellPrice: 20,
        buyPrice: 20,
        variants: [],
        variantsActive: false,
        included: false
    },
    {
        name: 'Dead Dog',
        sellPrice: 20,
        buyPrice: 20,
        variants: [],
        variantsActive: false,
        included: false
    }
]