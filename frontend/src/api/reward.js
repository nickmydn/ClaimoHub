import api from './axios'

export const getRewards = (params = {}) =>
  api.get('/mst-reward', { params })

export const getReward = (id) =>
  api.get(`/mst-reward/${id}`)

export const createReward = (data) =>
  api.post('/mst-reward', data)

export const updateReward = (id, data) =>
  api.put(`/mst-reward/${id}`, data)

export const deleteReward = (id) =>
  api.delete(`/mst-reward/${id}`)
