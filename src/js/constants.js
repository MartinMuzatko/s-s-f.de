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
        name: 'Fennek',
        minPrice: 10,
        summary: 'Danke für deine Unterstützung! Dich erwartet ein kleines Dankeschön'
    },
    {
        name: 'Wolf',
        minPrice: 20,
        summary: ''
    },
    {
        name: 'Tiger',
        minPrice: 30,
        summary: 'Danke für deine Unterstützung! Dich erwartet ein kleines Dankeschön'
    }
]