import api from './axios'

export const getVoucherProducts = (params = {}) =>
  api.get('/mst-vch-product', { params })

export const getVoucherProduct = (id) =>
  api.get(`/mst-vch-product/${id}`)

export const createVoucherProduct = (data) =>
  api.post('/mst-vch-product', data)

export const updateVoucherProduct = (id, data) =>
  api.put(`/mst-vch-product/${id}`, data)

export const deleteVoucherProduct = (id) =>
  api.delete(`/mst-vch-product/${id}`)
