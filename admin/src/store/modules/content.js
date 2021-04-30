// initial state
const state = {
  blankEntity: {
    id: '',
    model: '',
    view: '',
    contents: [],
    entity_relations: [],
    parent_entity_id: '',
    is_active: true,
    properties: {},
    published_at: null,
    unpublished_at: null,
    version: 0,
    version_full: 0,
    version_relations: 0,
    version_tree: 0,
    created_at: null,
    updated_at: null,
    updated_by: null
  }
}

export default {
  namespaced: false,
  state
}
