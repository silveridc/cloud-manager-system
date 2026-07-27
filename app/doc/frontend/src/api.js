import axios from 'axios'

const api = axios.create({
  baseURL: '/doc',
  timeout: 30000,
})

export function getList() {
  return api.get('/list').then(res => res.data)
}

export function getInfo(name) {
  return api.get('/info', { params: { name } }).then(res => res.data)
}

export function search(query) {
  return api.get('/search', { params: { query } }).then(res => res.data)
}

export function login(pass) {
  return api.post('/login', new URLSearchParams({ pass })).then(res => res.data)
}

export function debug(data) {
  return api.post('/debug', new URLSearchParams(data)).then(res => res.data)
}
