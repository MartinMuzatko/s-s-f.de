import test from 'ava'
import * as api from './src/js/api'


test('test that ava works', t => {
    t.pass()
})

test('api is loaded', t => {
    console.log(api);
    t.truthy((typeof api.createEvent == 'function'))
})
