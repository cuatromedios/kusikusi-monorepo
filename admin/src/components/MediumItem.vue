<template>
  <q-card
    class="medium-item"
    :class="{ 'cursor-drag': reorderMode }">
    <q-img :src="`${$store.getters.media_url}${medium.thumb}?${Math.random()}`"
           :ratio="1" contain
           v-if="(medium.properties && medium.properties.isWebImage && medium.properties.format !== 'svg') && !uploading"
           class="checkered-bg" />
    <q-img :src="`${$store.getters.media_url}${medium.original}?${Math.random()}`"
           :ratio="1" contain
           v-if="(medium.properties && medium.properties.isWebImage && medium.properties.format === 'svg') && !uploading"
           class="checkered-bg" />
    <q-responsive :ratio="1" v-if="(!medium.properties || !medium.properties.isWebImage) || uploading" >
      <div class="rounded-borders bg-grey-2 text-white flex flex-center">
        <q-icon size="80px"
                color="grey-5"
                :name="!medium.properties ? 'blur_on' : medium.properties.isImage ? 'image' : medium.properties.isVideo ? 'movie' : medium.properties.isAudio ? 'graphic_eq' : medium.properties.isDocument ? 'description' : 'insert_drive_file'"
                v-if="!medium.properties || !medium.properties.isWebImage" />
      </div>
    </q-responsive>
    <q-icon name="cloud_upload"
            class="absolute-top-left"
            size="lg"
            v-show="false"
            style="left: 16px; top: 16px; opacity: 0.5" />
    <q-file
      @input="updateFile"
      label="" outlined rounded
      ref="filePicker"
      v-show="false"
      class="absolute-top-left"
      style="width: 4em; height: 4em; left: 8px; top: 8px;"
      :accept="acceptedFile"
      :loading="uploading"
      @rejected="rejectedFile"
    >
      <template v-slot:file></template>
    </q-file>
    <q-btn-dropdown round dropdown-icon="more_vert" flat color="grey-6" class="absolute-top-right">
      <q-list>
        <q-item clickable v-close-popup :to="{ name: 'content', params: { entity_id: medium.id } }" target="_blank">
          <q-item-section avatar>
            <q-icon name="launch" />
          </q-item-section>
          <q-item-section>
            <q-item-label>{{ $t('media.edit') }}</q-item-label>
          </q-item-section>
        </q-item>
        <q-item clickable v-close-popup @click="onUnlink">
          <q-item-section avatar>
            <q-icon name="link_off" />
          </q-item-section>
          <q-item-section>
            <q-item-label>{{ $t('media.unlink') }}</q-item-label>
          </q-item-section>
        </q-item>
        <q-item clickable v-close-popup @click="$refs.filePicker.pickFiles()">
          <q-item-section avatar>
            <q-icon name="cloud_upload" />
          </q-item-section>
          <q-item-section>
            <q-item-label>{{ $t('media.replace') }}</q-item-label>
          </q-item-section>
        </q-item>
      </q-list>
    </q-btn-dropdown>
    <q-separator/>
    <q-card-actions side class="row">
      <h3 class="col-12" style="word-break: break-all">
        <q-icon :name="!medium.properties ? 'blur_on' : medium.properties.isImage ? 'image' : medium.properties.isVideo ? 'movie' : medium.properties.isAudio ? 'graphic_eq' : medium.properties.isDocument ? 'description' : 'insert_drive_file'" class="q-mr-xs" />
        {{ medium.content.title || $t($store.getters.nameOf(medium.model))}}
        <span v-if="medium.properties" class="text-grey-8">({{ medium.properties.format }})</span>
      </h3>
      <div v-if="tags && tags.length > 0" class="row items-center col-12">
        <q-select v-model="editingTags"
                  @input="onInput"
                  :options="tags"
                  multiple use-chips
                  ref="tagSelector"
                  class="tag-select"
                  dense borderless>
          <template v-slot:append>
            <q-icon name="local_offer" size="xs" color="info" /><small>{{ $t('contents.addTag') }}</small>
          </template>
        </q-select>
        <q-btn dense outline rounded icon="check" size="sm" color="positive" v-if="editing" :loading="saving" @click="acceptTags">{{ $t('general.confirm') }}&nbsp;&nbsp;</q-btn>
        <q-btn dense flat rounded size="sm" color="grey" v-if="editing" @click="cancelTags">{{ $t('general.cancel') }}</q-btn>
      </div>
    </q-card-actions>
  </q-card>
</template>
<script>
import _ from 'lodash'
export default {
  name: 'MediaItem',
  props: ['medium', 'tags', 'reorderMode', 'entity_id', 'allowed'],
  data () {
    return {
      uploading: false,
      saving: false,
      editing: false,
      editingTags: [],
      storedTags: []
    }
  },
  mounted () {
    this.editingTags = _.clone(this.medium.medium.tags)
    this.storedTags = _.clone(this.medium.medium.tags)
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
      await this.$api.post(`/entities/${this.entity_id}/relations/${this.medium.id}/medium`, {
        tags: this.storedTags,
        position: this.medium.media_position,
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
    onUnlink () {
      this.$q.dialog({
        title: this.$t('media.unlink'),
        ok: {
          label: this.$t('general.ok'),
          color: 'primary'
        },
        cancel: {
          label: this.$t('general.cancel'),
          color: 'grey',
          flat: true
        },
        message: this.$t('media.unlinkConfirm')
      }).onOk(async () => {
        await this.$api.delete(`/entities/${this.entity_id}/relations/${this.medium.id}/medium`)
        this.$emit('getMedia')
      })
    },
    async updateFile (file) {
      const data = new FormData()
      data.append('file', file)
      this.uploading = true
      await this.$api.post(`/media/${this.medium.id}/upload`, data, { 'Content-Type': 'multipart/form-data' })
      this.uploading = false
    },
    rejectedFile (files) {
      let filesNames = ''
      for (const f in files) {
        filesNames += (' - ' + files[f].file.name)
      }
      this.$q.notify({
        position: 'top',
        color: 'negative',
        message: this.$t('media.invalidFormat'),
        caption: filesNames
      })
    }
  },
  computed: {
    acceptedFile () {
      if (!this.allowed || this.allowed[0] === '*' || this.allowed[0] === '' || this.allowed === '' || this.allowed === '*') {
        return ''
      }
      let formatList = []
      _.each(this.allowed, (f) => {
        formatList = [...formatList, ..._.get(this.$store.state.ui.config, `formats.${f}`, [f])]
      })
      return '.' + formatList.join(', .')
    }
  }
}
</script>
<style lang="scss">
  $checkcolor: #eeeeee;
  .checkered-bg {
    background-image: linear-gradient(45deg, $checkcolor 25%, transparent 25%), linear-gradient(-45deg, $checkcolor 25%, transparent 25%), linear-gradient(45deg, transparent 75%, $checkcolor 75%), linear-gradient(-45deg, transparent 75%, $checkcolor 75%);
    background-size: 20px 20px;
    background-position: 0 0, 0 10px, 10px -10px, -10px 0px;
  }
</style>
