<template>
  <div>
    <div v-if="loading" class="flex column items-end">
      <q-skeleton type="QBtn" class="q-mb-md" />
      <q-skeleton type="QInput" class="q-mb-md full-width" />
      <q-skeleton type="QInput" class="q-mb-md full-width" />
    </div>
    <q-banner class="bg-grey-2 text-center q-mb-md" v-if="!readonly">{{ $t('general.saveBefore')}}</q-banner>
    <div v-if="!loading && readonly">
      <div class="q-mb-md flex justify-end">
        <q-btn class=""
               outline color="positive"  icon="add_circle"
               :label="`${$t('general.add')} / ${$t('general.edit')}`"
               @click="addRelationsDialog = true"/>
      </div>
      <q-banner class="bg-grey-2 text-center q-mb-md" v-if="!relations || (relations && relations.length === 0)">{{ $t('general.noItems')}}</q-banner>
      <q-list bordered>
        <draggable v-model="relations" v-bind="getDragOptions()" :disabled="!reorderMode">
          <relation-item v-for="relation in relations"
                      :key="relation.relation_id"
                      :relation="relation"
                      :entity_id="entity.id"
                      @getRelations="getRelations"
                      :tags="tags"
                      :reorderMode="reorderMode"
          />
        </draggable>
      </q-list>
      <q-btn v-if="canReorder && !reorderMode" flat icon="swap_vert" :label="$t('contents.reorder')" class="q-mt-sm" @click="startReorder" />
      <q-btn v-if="reorderMode" :loading="reordering" :disable="reordering" flat icon="done" :label="$t('general.confirm')" class="q-mt-sm" color="positive" @click="reorder" />
      <q-btn v-if="reorderMode" flat :label="$t('general.cancel')" class="q-mt-sm" color="grey" @click="cancelReorder" />
    </div>
    <q-dialog v-model="addRelationsDialog" @before-hide="onRelationsDialogHide" @before-show="onRelationsDialogShow" persistent >
      <q-card style="width: 90vw">
        <q-card-section>
          <div v-if="loadingRelationables" class="flex column items-end">
            <q-skeleton type="QInput" class="q-mb-md full-width" />
            <q-skeleton type="QInput" class="q-mb-md full-width" />
          </div>
          <q-list bordered v-if="!loadingRelationables && relationables.length > 0">
            <q-item clickable class="child-item" v-for="relationable in relationables" :key="relationable.id" @click="addRelationable(relationable)">
              <q-item-section avatar top>
                <div class="flex items-center item-avatar">
                  <span v-for="space in relationable.descendant_relation_depth" :key="space">&nbsp;&nbsp;&nbsp;</span>
                  <q-avatar :icon="$store.getters.iconOf(relationable.model)" color="grey" text-color="white" size="md" />
                </div>
              </q-item-section>
              <q-item-section>
                <q-item-label>
                  <h3>
                    {{ relationable.title || $t($store.getters.nameOf(relationable.model))}}
                  </h3>
                </q-item-label>
              </q-item-section>
              <q-item-section side>
                <q-icon name="check_circle" :color="relationable.isSelected ? 'positive' : 'grey-4'" />
              </q-item-section>
            </q-item>
          </q-list>
        </q-card-section>
        <q-card-actions align="center">
          <q-btn
            color="grey"
            flat
            :label="$t('general.cancel')"
            v-close-popup
            class="q-mr-md"
          />
          <q-btn
            color="primary"
            :label="$t('general.confirm')"
            @click="save"
            :loading="loading"
          />
        </q-card-actions>
      </q-card>
    </q-dialog>
  </div>
</template>
<script>
import draggable from 'vuedraggable'
import _ from 'lodash'
import RelationItem from './RelationItem'
export default {
  components: { RelationItem, draggable },
  name: 'Relations',
  props: {
    entity: {},
    readonly: {
      type: Boolean,
      default: true
    },
    kind: {
      type: String,
      default: ''
    },
    from_entity_id: {
      type: String,
      default: ''
    },
    list: {
      type: String,
      default: 'children'
    },
    tags: {
      type: Array,
      default: () => []
    },
    models: {
      type: Array,
      default: () => []
    }
  },
  data () {
    return {
      loading: true,
      relations: [],
      relationsIds: {},
      storedRelations: [],
      reorderMode: false,
      reordering: false,
      addRelationsDialog: false,
      uploadProgress: [],
      uploading: false,
      loadingRelationables: false,
      relationables: []
    }
  },
  mounted () {
    this.getRelations()
  },
  methods: {
    async getRelations () {
      this.loading = true
      const relationsResult = await this.$api.get(`/entities?related-by=${this.entity.id}:${this.kind}&select=id,model,contents.title&only-published=false&order-by=relation.position&per-page=100`)
      this.loading = false
      if (relationsResult.success) {
        this.relations = relationsResult.data.data
        this.relationsIds = {}
        this.relations.forEach((v) => {
          this.relationsIds[v.id] = true
        })
      } else {
        this.$q.notify({
          position: 'top',
          color: 'negative',
          message: this.$t('general.serverError')
        })
      }
    },
    getDragOptions () {
      return { animation: 500, sort: true }
    },
    startReorder () {
      this.storedRelations = _.cloneDeep(this.relations)
      this.reorderMode = true
    },
    cancelReorder () {
      this.relations = _.cloneDeep(this.storedRelations)
      this.reorderMode = false
    },
    async reorder () {
      this.reordering = true
      const relation_ids = _.flatMap(this.relations, (c) => c.relation_id)
      const reorderResult = await this.$api.patch('/entities/relations/reorder', { relation_ids })
      this.reordering = false
      if (reorderResult.success) {
        this.reorderMode = false
      } else {
        this.$q.notify({
          position: 'top',
          color: 'negative',
          message: this.$t('general.serverError')
        })
      }
    },
    onRelationsDialogHide () {
      this.getRelations()
    },
    async onRelationsDialogShow () {
      this.loadingRelationables = true
      let childrenOrDescendants
      if (this.list === 'descendants') {
        childrenOrDescendants = 'descendant-of'
      } else {
        childrenOrDescendants = 'child-of'
      }
      const relationsResult = await this.$api.get(`/entities?${childrenOrDescendants}=${this.from_entity_id}&select=id,parent_entity_id,model,contents.title&only-published=false&order-by=depth,position&per-page=100`)
      if (relationsResult.success) {
        if (this.list === 'descendants') {
          const hashArr = {}
          for (let i = 0; i < relationsResult.data.data.length; i++) {
            relationsResult.data.data[i].isSelected = !!this.relationsIds[relationsResult.data.data[i].id]
            if (hashArr[relationsResult.data.data[i].parent_entity_id] === undefined) hashArr[relationsResult.data.data[i].parent_entity_id] = []
            hashArr[relationsResult.data.data[i].parent_entity_id].push(relationsResult.data.data[i])
          }
          this.relationables = this.treeSort(hashArr, this.from_entity_id, [])
        } else {
          this.relationables = relationsResult.data.data
        }
      }
      this.loadingRelationables = false
    },
    treeSort (hashArr, key, result) {
      // Thank you https://stackoverflow.com/questions/35329658/sort-array-of-objects-with-hierarchy-by-hierarchy-and-name
      if (hashArr[key] === undefined) return
      const arr = hashArr[key].sort((a, b) => { return a.descendant_relation_position > b.descendant_relation_position })
      for (let i = 0; i < arr.length; i++) {
        result.push(arr[i])
        this.treeSort(hashArr, arr[i].id, result)
      }
      return result
    },
    addRelationable (item) {
      item.isSelected = !item.isSelected
    },
    async save () {
      this.loading = true
      const bulk = []
      for (let i = 0; i < this.relationables.length; i++) {
        if (!this.relationsIds[this.relationables[i].id] && this.relationables[i].isSelected) {
          bulk.push({
            method: 'POST',
            url: `/entity/${this.entity.id}/relation`,
            data: {
              called_entity_id: this.relationables[i].id,
              kind: this.kind,
              tags: [],
              position: this.relations.length + 1 + i,
              depth: 1
            }
          })
        } else if (this.relationsIds[this.relationables[i].id] && !this.relationables[i].isSelected) {
          bulk.push({
            method: 'DELETE',
            url: `/entity/${this.entity.id}/relation/${this.relationables[i].id}/${this.kind}`
          })
        }
      }
      await this.$api.bulkCall(bulk, true)
      await this.getRelations()
      this.loading = false
      this.addRelationsDialog = false
    }
  },
  computed: {
    canReorder () {
      return true
    }
  }
}
</script>

<style lang="scss">
  .cursor-drag {
    cursor: grab;
  }
  .sortable-chosen {
    outline: 1px solid lightgrey;
    background-color: white;
  }
  .sortable-ghost {
    cursor: grab;
  }
  .children-move {
    transition: transform 0.5s;
  }
  .q-file.q-field--outlined.media-library-file-picker {
    input {
      padding: 1em;
    }
    .q-field__label {
      text-align: center;
      margin-top: 11px;
    }
    .q-field__control {
      height: 45vh;
      &:before {
        border-style: dashed;
      }
    }
  }
  .q-file.q-field--outlined.media-library-file-picker.with-files {
    .q-field__control {
      height: auto;
    }
  }
  .media-library-file-list .q-input small {
    font-size: 0.75rem;
  }
</style>
