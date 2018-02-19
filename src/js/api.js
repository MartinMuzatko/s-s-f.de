export const BASEURL = `${window.api.url}api`

// TEST STREAM

export async function uploadAvatar(file, user) {
    console.log(file);
    let x= new FormData()
    x.append('file', file)
	const response = await fetch(
        `${BASEURL}/users/${user}/avatar`,
        {method: 'POST', body: x}
    )
    return response.text()
}


// EVENTS

export async function createEvent(data) {
    const response = await fetch(`${BASEURL}/events/`, {method: 'POST', body: JSON.stringify(data)})
    return response.text()
}

export async function getEventData(eventName) {
    const response = await fetch(`${BASEURL}/events/${eventName}`, {method: 'GET'})
    return response.json()
}

export async function getRegistrations(eventName) {
    const response = await fetch(`${BASEURL}/events/${eventName}/registrations`, {method: 'GET'})
    return response.json()
}

// EVENT REGISTER

export async function registerUser(eventName, userName, data) {
    const response = await fetch(
        `${BASEURL}/events/${eventName}/registrations/${userName}`,
        {method: 'POST', body: data, credentials: 'same-origin'}
    )
    return response.json()
}

export async function updateRegistrations(eventName, data) {
    const response = await fetch(
        `${BASEURL}/events/${eventName}/registrations`,
        { method: 'PUT', body: JSON.stringify(data), credentials: 'same-origin' }
    )
    return response.json()
}

export async function warnUser(eventName, userName, data) {
    const response = await fetch(
        `${BASEURL}/events/${eventName}/registrations/${userName}/warnings`,
        { method: 'POST', body: JSON.stringify(data), credentials: 'same-origin' }
    )
    return response.json()
}

// PAGE EDITOR

export async function getModules() {
    const response = await fetch(`${BASEURL}/modules`, {credentials: 'same-origin'})
    return response.json()
}

export async function getCountries() {
    const response = await fetch(`${BASEURL}/countries`, {credentials: 'same-origin'})
    return response.json()
}

export async function getTemplates() {
    const response = await fetch(`${BASEURL}/templates`, {credentials: 'same-origin'})
    return response.json()
}

export async function getDefaultNotifications() {
    const response = await fetch(`${BASEURL}/notifications`, { credentials: 'same-origin' })
    return response.json()
}

export async function updatePage(pageId, modules) {
    const response = await fetch(
        `${BASEURL}/pages/${pageId}`,
        {
            credentials: 'same-origin',
            method: 'PUT',
            body: JSON.stringify(modules)})
    return response.text()
}

export async function getPermissions() {
    const response = await fetch(`${BASEURL}/permissions?name=event-user`, {credentials: 'same-origin'})
    return response.json()
}

export async function getDefaultRoles() {
    const response = await fetch(`${BASEURL}/roles`, {credentials: 'same-origin'})
    return response.json()
}

export async function getDefaultItems() {
    const response = await fetch(`${BASEURL}/items`, {credentials: 'same-origin'})
    return response.json()
}

export async function getPageModules(id) {
    const response = await fetch(`${BASEURL}/pages/${id}`, {credentials: 'same-origin'})
    return await response.json()
}

// USER

export async function getUsers(username, limit=10) {
    username = encodeURIComponent(username)
    const response = await fetch(`${BASEURL}/users/?name=${username}&limit=${limit}`, {credentials: 'same-origin'})
    return response.json()
}

export async function getSpecies(username, limit = 10) {
    const response = await fetch(`${BASEURL}/users/getSpecies?name=${username}&limit=${limit}`)
    return response.json()
}



export async function getUserAvatar(username) {
    const response = await fetch(`${BASEURL}/users/getAvatar?name=${username}`)
    return response.json()
}

// USER MESSAGES

export async function sendMessage(message) {
    const response = await fetch(
        `${BASEURL}/users/${message.receiver}/messages`,
        {
            method: 'POST',
            credentials: 'same-origin',
            body: JSON.stringify(message)
        }
    )
    return response.json()
}

export async function readMessage(user, id) {
    const response = await fetch(`${BASEURL}/users/${user}/messages/${id}`, {credentials: 'same-origin'})
    return response.json()
}

export async function getMessages(user, route) {
    const response = await fetch(`${BASEURL}/users/${user}/messages?mode=${route}`, {credentials: 'same-origin'})
    return response.json()
}
