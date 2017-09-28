import wNumb from 'wnumb'

export const GOOGLE_MAPS_API = 'AIzaSyBHi3lZRAAQxCSof6j5lYB775VIIDWbrck'
export const moneyFormat = wNumb({
    mark: ',',
    thousand: '.',
    suffix: ' €',
    decimals : 2
})
