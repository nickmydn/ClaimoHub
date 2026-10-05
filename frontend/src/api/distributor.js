import api from './axios'

export const getDistributors = () => {
  return api.get('/mst-distributor')
}

export const getDistributor = (id) => {
  return api.get(`/mst-distributor/${id}`)
}

export const createDistributor = (data) => {
  return api.post('/mst-distributor', data)
}

export const updateDistributor = (id, data) => {
  return api.put(`/mst-distributor/${id}`, data)
}

export const deleteDistributor = (id) => {
  return api.delete(`/mst-distributor/${id}`)
}

export const getDistributorMerchants = (id) => {
  return api.get(`/mst-distributor/${id}/merchants`)
}
