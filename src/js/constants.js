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

export const STATUS_NEW = 'new'
export const STATUS_PENDING = 'pending'
export const STATUS_ACCEPTED = 'accepted'
export const STATUS_WAITING = 'waiting'
export const STATUS_SIGNEDOFF = 'signedoff'
export const STATUS_DISMISSED = 'dismissed'

export const PAYMENT_CASH = 'cash'
export const PAYMENT_DEBIT = 'debit'

export const PAYMENT = {
    [PAYMENT_CASH]: 'Barzahlung',
    [PAYMENT_DEBIT]: 'Überweisung',
}


export const USERSTATUS = [
    STATUS_NEW,
    STATUS_PENDING,
    STATUS_ACCEPTED,
    STATUS_WAITING,
    STATUS_SIGNEDOFF,
    STATUS_DISMISSED,
]

export const STATUS = {
    [STATUS_NEW]: 'Neu',
    [STATUS_PENDING]: 'Zahlung ausstehend',
    [STATUS_ACCEPTED]: 'Akzeptiert',
    [STATUS_WAITING]: 'Warteliste',
    [STATUS_SIGNEDOFF]: 'Abgemeldet',
    [STATUS_DISMISSED]: 'Verbannt',
}