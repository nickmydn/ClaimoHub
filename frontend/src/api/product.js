import api from './axios'

export const getProducts = () => api.get('/mst-product')
export const getProduct = (id) => api.get(`/mst-product/${id}`)
export const createProduct = (data) => api.post('/mst-product', data)
export const updateProduct = (id, data) =>
  api.put(`/mst-product/${id}`, data)
export const deleteProduct = (id) =>
  api.delete(`/mst-product/${id}`)
