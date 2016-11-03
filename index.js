import riot from 'riot'
import App from './components/app.html'
document.body.innerHTML = '<app></app>'
riot.mount('*')
console.log(App);
