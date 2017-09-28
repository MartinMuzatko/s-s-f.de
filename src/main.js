import 'riot'
import fittier from 'fitter-happier-text'
import 'waypoints/lib/noframework.waypoints'
import './less/main.less'
import './flex.scss'
import './js/components/page/page-editor.html'
import './js/components/ssf-carousel.html'
import './js/components/ssf-countdown.html'
import './js/components/ssf-chart.html'
import './js/components/ssf-location-distance.html'
import './js/components/user/user-register.html'
import './js/components/user/user-login.html'
import './js/components/user/user-profile-dropdown.html'
import './js/components/user/user-profile-card.html'
import './js/components/user/user-profile-list.html'
import './js/components/user/user-messages.html'
import './js/components/user/user-club-list.html'
import './js/components/user/user-filter.html'
import './js/components/user/user-message-send.html'
import './js/components/event/edit/event-update.html'
import './js/components/event/edit/event-create.html'
import './js/components/event/event-register.html'
import './js/components/event/manage/manage-registrations.html'
import './js/components/event/event-list-short.html'
import './images/logo.png'
import './images/navigation/events.svg'
import './images/navigation/verein.svg'
import './images/navigation/users.svg'
import './images/navigation/home.svg'
import './images/navigation/kontakt.svg'
import './images/navigation/login.svg'

fittier(document.querySelectorAll('.js-fitty'))

function floatHeaderOnWaypoint () {
    let offset = document.querySelector('.site__nav').offsetHeight
    new Waypoint({
        element: document.querySelector('.site__content'),
        offset: offset,
        handler: direction=>{
            let offset = document.querySelector('.site__nav').offsetHeight
            if (direction == 'down') {
                if (document.body.clientWidth > 600) {
                    document.querySelector('.site__nav').classList.add('site__nav--floating')
                    document.querySelector('.site__content').style.marginTop = offset+16+'px'
                }
            } else {
                document.querySelector('.site__nav').classList.remove('site__nav--floating')
                document.querySelector('.site__content').style.marginTop = 0
            }
        }
    })
}

window.riot = riot
window.api = window.api || {}
window.api.google = {}
window.api.user.track = {}
riot.observable(window.api.google)
riot.observable(window.api.user.track)
riot.mount('*')

floatHeaderOnWaypoint()
