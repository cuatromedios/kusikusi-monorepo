<template>
  <q-item
    class="child-item"
    :class="{ 'cursor-drag': reorderMode }">
    <q-item-section avatar top>
      <div class="flex items-center item-avatar">
        <q-icon name="drag_indicator" size="md" color="grey" v-if="reorderMode" />
        <q-avatar :icon="$store.getters.iconOf(child.model)" color="grey" text-color="white" :class="{ 'cursor-drag': reorderMode, 'disabled': !child.is_active }" />
      </div>
    </q-item-section>
    <q-item-section>
      <q-item-label>
        <h3>
          <span v-if="reorderMode">{{ child.content.title ||  child.properties.title || $t($store.getters.nameOf(child.model)) }}</span>
          <router-link v-if="!reorderMode" :to="{ name: 'content', params: { entity_id:child.id } }" >{{ child.content.title || child.properties.title || $t($store.getters.nameOf(child.model))}}</router-link>
        </h3>
      </q-item-label>
      <q-item-label caption lines="1">
        {{ $t($store.getters.nameOf(child.model)) }} |
        <span :class=" { 'text-warning': !isBefore(child.published_at) } ">{{ child.published_at | moment('dddd DD MMMM YYYY, h:mm a') }}</span> |
        <span :class=" { 'text-warning': child.visibility !== 'public', 'text-positive': child.visibility === 'public' } ">{{ child.visibility }}</span>
      </q-item-label>
    </q-item-section>
    <q-item-section side v-if="tags && tags.length > 0">
      <div class="row items-center tag-select">
        <q-select v-model="editingTags"
                  @input="onInput"
                  :options="tags"
                  multiple use-chips
                  ref="tagSelector"
                  dense options-dense borderless>
          <template v-slot:append>
            <q-icon name="local_offer" size="xs" color="info" /> <small>{{ $t('contents.addTag') }}</small>
          </template>
        </q-select>
        <q-btn dense outline rounded icon="check" size="sm" color="positive" v-if="editing" :loading="saving" @click="acceptTags">{{ $t('general.confirm') }}&nbsp;&nbsp;</q-btn>
        <q-btn dense flat rounded size="sm" color="grey" v-if="editing" @click="cancelTags">{{ $t('general.cancel') }}</q-btn>
      </div>
    </q-item-section>
  </q-item>
</template>
<script>
import _ from 'lodash'
import moment from 'moment'
export default {
  name: 'ChildItem',
  props: ['child', 'tags', 'reorderMode', 'entity_id'],
  data () {
    return {
      saving: false,
      editing: false,
      editingTags: [],
      storedTags: []
    }
  },
  mounted () {
    this.editingTags = _.clone(this.child.child.tags)
    this.storedTags = _.clone(this.child.child.tags)
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
      await this.$api.post(`/entities/${this.child.id}/relations/${this.entity_id}/ancestor`, {
        tags: this.storedTags,
        position: this.child.child_relation_position,
        depth: 1
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
    isBefore (date1, date2) {
      if (!date2) date2 = moment()
      return moment(date1).isBefore(date2)
    }
  }
}
</script>
