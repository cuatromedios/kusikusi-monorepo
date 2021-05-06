<template>
  <div>
    <div v-if="loading" class="flex column items-end">
        <q-skeleton type="QBtn" class="q-mb-md" />
        <q-skeleton type="QInput" class="q-mb-md full-width" />
        <q-skeleton type="QInput" class="q-mb-md full-width" />
        <q-skeleton type="QInput" class="q-mb-md full-width" />
    </div>
    <q-banner class="bg-grey-2 text-center" v-if="!readonly">{{ $t('general.saveBefore')}}</q-banner>
    <div v-if="!loading && readonly">
      <div class="q-mb-md flex justify-end">
        <q-btn-dropdown class="" outline color="positive"  icon="add_circle"  :label="$t('general.add')" v-if="models && models.length > 1">
          <q-list>
            <q-item clickable v-close-popup
                    v-for="model in models"
                    @click="add(model)"
                    :key="model">
              <q-item-section>
                <q-item-label>{{ $t($store.getters.nameOf(model)) }}</q-item-label>
              </q-item-section>
            </q-item>
          </q-list>
        </q-btn-dropdown>
        <q-btn class=""
               outline color="positive"  icon="add_circle"
               :label="`${$t('general.add')} ${$t($store.getters.nameOf(models[0]))}`"
               v-if="models && models.length === 1"
               @click="add(models[0])"/>
      </div>
      <q-banner class="bg-grey-2 text-center" v-if="!children || (children && children.length === 0)">{{ $t('general.noItems')}}</q-banner>
      <q-list bordered>
        <draggable v-model="children" v-bind="getDragOptions()" :disabled="!reorderMode">
          <child-item v-for="child in children"
                      :key="child.id"
                      :child="child"
                      :entity_id="entity.id"
                      :tags="tags"
                      :reorderMode="reorderMode"
          />
        </draggable>
      </q-list>
      <div class="flex flex-center q-my-md" v-if="this.lastPage > 1">
        <q-pagination
          v-model="currentPage"
          :max="lastPage"
          :boundary-links="true"
          :disabled="this.loading"
          @input="changePage"
        />
      </div>
      <q-btn v-if="canReorder && !reorderMode" flat icon="swap_vert" :label="$t('contents.reorder')" class="q-mt-sm" @click="startReorder" />
      <q-btn v-if="reorderMode" :loading="reordering" :disable="reordering" flat icon="done" :label="$t('general.confirm')" class="q-mt-sm" color="positive" @click="reorder" />
      <q-btn v-if="reorderMode" flat :label="$t('general.cancel')" class="q-mt-sm" color="grey" @click="cancelReorder" />
    </div>
  </div>
</template>
<script>
import draggable from 'vuedraggable'
import _ from 'lodash'
import ChildItem from './ChildItem'
export default {
  components: { ChildItem, draggable },
  name: 'Children',
  props: {
    entity: {},
    readonly: {
      type: Boolean,
      default: true
    },
    ofModel: {
      type: Array,
      default: () => []
    },
    models: {
      type: Array,
      default: () => []
    },
    tags: {
      type: Array,
      default: () => []
    },
    orderBy: {
      type: String,
      default: 'relation_children.position'
    }
  },
  data () {
    return {
      loading: true,
      children: [],
      storedChildren: [],
      reorderMode: false,
      reordering: false,
      currentPage: 1,
      perPage: 1,
      lastPage: 1,
      total: 1,
      to: 1,
      from: 1
    }
  },
  mounted () {
    this.getChildren()
  },
  methods: {
    changePage (value) {
      this.currentPage = value
      this.getChildren()
    },
    async getChildren () {
      this.loading = true
      const childrenResult = await this.$api.get(`/entities?children-of=${this.entity.id}&select=contents.title,properties,published_at,unpublished_at,model,id&only-published=false&order-by=${this.orderBy}&page=${this.currentPage}`)
      this.loading = false
      if (childrenResult.success) {
        this.children = childrenResult.data.data
        this.currentPage = childrenResult.data.current_page
        this.perPage = childrenResult.data.per_page
        this.lastPage = childrenResult.data.last_page
        this.total = childrenResult.data.total
        this.to = childrenResult.data.to
        this.from = childrenResult.data.from
      } else {
        this.$q.notify({
          position: 'top',
          color: 'negative',
          message: this.$t('general.serverError')
        })
      }
    },
    add (model) {
      this.$router.push({ name: 'content', params: { entity_id: 'new', model: model, conector: 'in', parent_entity_id: this.entity.id } })
    },
    getDragOptions () {
      return { animation: 500, sort: true }
    },
    startReorder () {
      this.storedChildren = _.cloneDeep(this.children)
      this.reorderMode = true
    },
    cancelReorder () {
      this.children = _.cloneDeep(this.storedChildren)
      this.reorderMode = false
    },
    async reorder () {
      this.reordering = true
      const relation_ids = _.flatMap(this.children, (c) => {
        return c.child.relation_id
      })
      const reorderResult = await this.$api.patch(`/entities/${this.entity.id}/relations/reorder`, { relation_ids })
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
    }
  },
  computed: {
    canReorder () {
      return this.orderBy === 'relation_children.position'
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
</style>
