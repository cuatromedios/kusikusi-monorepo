<template>
  <q-item
    class="child-item"
    :class="{ 'cursor-drag': reorderMode }">
    <q-item-section avatar top>
      <div class="flex items-center item-avatar">
        <q-icon name="drag_indicator" size="md" color="grey" v-if="reorderMode" />
        <q-avatar :icon="$store.getters.iconOf(relation.model)" color="grey" text-color="white" size="md" />
      </div>
    </q-item-section>
    <q-item-section>
      <q-item-label>
        <h3>
          <span v-if="reorderMode">{{ relation.content.title || $t($store.getters.nameOf(relation.model)) }}</span>
          {{ relation.content.title || $t($store.getters.nameOf(relation.model))}}
        </h3>
      </q-item-label>
    </q-item-section>
    <!--q-item-section side>{{ relation.relation_position }}</q-item-section-->
    <q-item-section side v-if="tags && tags.length > 0">
      <div class="row items-center">
        <q-select v-model="editingTags"
                  @input="onInput"
                  :options="tags"
                  multiple use-chips
                  menu-anchor="top left"
                  menu-self="bottom middle"
                  ref="tagSelector"
                  dense borderless hide-dropdown-icon>
          <template v-slot:prepend>
            <q-icon name="local_offer" size="sm" color="info" />
          </template>
        </q-select>
        <q-btn dense outline rounded icon="check" size="sm" color="positive" v-if="editing" :loading="saving" @click="acceptTags">{{ $t('general.confirm') }}&nbsp;&nbsp;</q-btn>
        <q-btn dense flat rounded size="sm" color="grey" v-if="editing" @click="cancelTags">{{ $t('general.cancel') }}</q-btn>
      </div>
    </q-item-section>
    <q-item-section side v-if="tags && tags.length > 0">
      <q-select v-model="secondaryMenu"
                @input="onSubmenu"
                :options="[{ value: 'unlink', label: $t('contents.unlink')}]"
                menu-anchor="top left"
                menu-self="bottom middle"
                display-value=""
                dense borderless hide-dropdown-icon>
        <template v-slot:prepend>
          <q-icon name="more_vert" size="sm" color="grey-7" />
        </template>
      </q-select>
    </q-item-section>
  </q-item>
</template>
<script>
import _ from 'lodash'
export default {
  name: 'RelationItem',
  props: ['relation', 'tags', 'reorderMode', 'entity_id'],
  data () {
    return {
      saving: false,
      editing: false,
      editingTags: [],
      storedTags: [],
      secondaryMenu: null
    }
  },
  mounted () {
    this.editingTags = _.clone(this.relation.related_by.tags)
    this.storedTags = _.clone(this.relation.related_by.tags)
  },
  methods: {
    onInput () {
      this.$refs.tagSelector.hidePopup()
      this.editing = true
    },
    async acceptTags () {
      this.$refs.tagSelector.hidePopup()
      this.saving = true
      this.storedTags = _.clone(this.editingTags)
      await this.$api.post(`/entities/${this.entity_id}/relations/${this.relation.id}/${this.relation.related_by.kind}`, {
        tags: this.storedTags,
        position: this.relation.related_by.position,
        depth: 0
      })
      this.saving = false
      this.editing = false
    },
    cancelTags () {
      this.$refs.tagSelector.hidePopup()
      this.editingTags = _.clone(this.storedTags)
      this.editing = false
      this.saving = false
    },
    async onSubmenu (option) {
      switch (option.value) {
        case 'unlink':
          await this.$api.delete(`/entities/${this.entity_id}/relation/${this.relation.id}/${this.relation.relation_kind}`)
          this.$emit('getRelations')
          break
      }
    }
  }
}
</script>
