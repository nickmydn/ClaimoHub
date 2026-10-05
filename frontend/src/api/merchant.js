import api from './axios'

export const getMerchants = () => api.get('/mst-merchant')
export const getMerchant = (id) => api.get(`/mst-merchant/${id}`)
export const createMerchant = (data) => api.post('/mst-merchant', data)
export const updateMerchant = (id, data) =>
  api.put(`/mst-merchant/${id}`, data)
export const deleteMerchant = (id) =>
  api.delete(`/mst-merchant/${id}`)
