import regeneratorRuntime from "regenerator-runtime"
import riot from 'riot'
import './less/main.less'
import './flex.scss'
import './components/user/user-register.html'
import './components/user/user-profile.html'
import './components/event/event-create.html'
// document.body.innerHTML = '<app></app>'
riot.mount('*')
/*
var x = async function() {
	var response = await fetch('/')
	var data = await response.text();
	console.log(data)
}
x()
*/

import './images/logo.png'
