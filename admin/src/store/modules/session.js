import { LocalStorage } from 'quasar'
import _ from 'lodash'
import Vue from 'vue'

// initial state
const state = {
  user: {
    profile: ''
  },
  authtoken: ''
}

// getters
const getters = {
  hasAuthtoken (state) {
    return (state.authtoken !== '')
  },
  entitiesWithWritePermissions (state) {
    const entities = []
    for (let e = 0; e < _.get(state, 'user.permissions.length', 0); e++) {
      if (state.user.permissions[e].write !== 'none' && state.user.permissions[e].read !== 'none') {
        const entity = state.user.permissions[e].entity
        entity.write = state.user.permissions[e].write
        entity.read = state.user.permissions[e].read
        entities.push(entity)
      }
    }
    return entities
  },
  entitiesWithPermissions (state) {
    const entities = []
    for (let e = 0; e < _.get(state, 'user.permissions.length', 0); e++) {
      if (state.user.permissions[e].read !== 'none') {
        const entity = state.user.permissions[e].entity
        entity.write = state.user.permissions[e].write
        entity.read = state.user.permissions[e].read
        entities.push(entity)
      }
    }
    return entities
  }
}

// actions
const actions = {
  getLocalSession ({ dispatch, commit }) {
    const session = LocalStorage.getItem('kusikusi_session')
    if (!session || session === {}) {
      dispatch('resetUserData')
    } else {
      commit('setAuthtoken', session.authtoken)
      commit('setUser', session.user)
    }
    return session
  },
  resetUserData ({ commit }) {
    commit('setAuthtoken', '')
    commit('setUser', {})
  }
}

// mutations
const mutations = {
  setAuthtoken (state, newToken) {
    state.authtoken = newToken
    Vue.prototype.$api.setAuthorization(newToken)
    const session = LocalStorage.getItem('kusikusi_session') || {}
    session.authtoken = newToken
    LocalStorage.set('kusikusi_session', session)
  },
  setUser (state, newUser) {
    state.user = newUser
  }
}

export default {
  namespaced: false,
  state,
  getters,
  actions,
  mutations
}
