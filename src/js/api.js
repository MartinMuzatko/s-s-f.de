export const BASEURL = '/s-s-f.de/api'

// TEST STREAM

export async function upload(file) {
    console.log(file);
	const response = await fetch(
        '/s-s-f.de/api/test',
        {method: 'POST', body: file}
    )
    return response.text()
}


// EVENTS

export async function createEvent(data) {
    const response = await fetch(`/s-s-f.de/api/events/`, {method: 'POST', body: JSON.stringify(data)})
    return response.text()
}

export async function getEventData(eventName) {
    const response = await fetch(`/s-s-f.de/api/events/${eventName}`, {method: 'GET'})
    return response.json()
}

// EVENT REGISTER

export async function registerUser(eventName, userName, data) {
    const response = await fetch(
        `/s-s-f.de/api/events/${eventName}/registrations/${userName}`,
        {method: 'POST', body: data, credentials: 'same-origin'}
    )
    return response.json()
}

// PAGE EDITOR

export async function getModules() {
    const response = await fetch(`/s-s-f.de/api/modules`, {credentials: 'same-origin'})
    return response.json()
}

export async function getPageModules(id) {
    const response = await fetch(`/s-s-f.de/api/pages/${id}`, {credentials: 'same-origin'})
    return await response.json()
}

// USER

export async function getUsers(username) {
    const response = await fetch(`/s-s-f.de/api/users/?name=${username}`, {credentials: 'same-origin'})
    return response.json()
}

export async function getUserAvatar(username) {
    const response = await fetch(`/s-s-f.de/api/users/getAvatar?name=${username}`)
    return response.json()
}

// USER MESSAGES

export async function sendMessage(message) {
    const response = await fetch(
        `/s-s-f.de/api/users/${message.receiver}/messages`,
        {
            method: 'POST',
            credentials: 'same-origin',
            body: JSON.stringify(message)
        }
    )
    return response.json()
}

export async function readMessage(user, id) {
    const response = await fetch(`/s-s-f.de/api/users/${user}/messages/${id}`, {credentials: 'same-origin'})
    return response.json()
}

export async function getMessages(user, route) {
    const response = await fetch(`/s-s-f.de/api/users/${user}/messages?mode=${route}`, {credentials: 'same-origin'})
    return response.json()
}
