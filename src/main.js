import 'riot'
import './less/main.less'
import './flex.scss'
import './js/components/event/edit/event-create.html'
import './js/components/user/user-register.html'
import './js/components/user/user-profile-dropdown.html'
import './js/components/user/user-profile.html'
import './js/components/user/user-profile-list.html'
import './js/components/user/user-messages.html'
import './js/components/event/event-list-short.html'
import './js/components/user/user-club-list.html'
import './js/components/user/user-filter.html'
import './js/components/user/user-message-send.html'

window.riot = riot
window.api = window.api || {}
window.api.google = {}
window.api.user.track = {}
riot.observable(window.api.google)
riot.observable(window.api.user.track)
riot.mount('*')

import './images/logo.png'
