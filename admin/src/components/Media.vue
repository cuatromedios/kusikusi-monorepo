<template>
  <div>
    <div v-if="loading" class="row q-col-gutter-md">
      <q-skeleton type="QBtn" class="col-3 offset-9 q-mt-md" />
      <div class="col-xs-12 col-sm-6 col-md-4" v-for="index in 3" :key="index">
        <q-card flat bordered class="full-width">
          <q-skeleton height="200px" square animation="fade" />
          <q-card-section>
            <q-skeleton type="text" class="text-subtitle2" animation="fade" />
            <q-skeleton type="text" width="50%" class="text-subtitle2" animation="fade" />
          </q-card-section>
        </q-card>
      </div>
    </div>
    <q-banner class="bg-grey-2 text-center q-mb-md" v-if="!readonly">{{ $t('general.saveBefore')}}</q-banner>
    <div v-if="!loading && readonly">
      <div class="q-mb-md flex justify-end">
        <q-btn class=""
               outline color="positive"  icon="add_circle"
               :label="$t('general.add')"
               v-if="allowed && allowed.length > 0"
               @click="addMediaDialog = true"/>
      </div>
      <q-banner class="bg-grey-2 text-center q-mb-md" v-if="!media || (media && media.length === 0)">{{ $t('general.noItems')}}</q-banner>
      <draggable v-model="media" v-bind="getDragOptions()" :disabled="!reorderMode" class="row draggable-container q-col-gutter-md">
        <div v-for="medium in media"
             :key="medium.id"
             class="col-xs-12 col-sm-6 col-md-4">
          <medium-item :medium="medium"
                       :entity_id="entity.id"
                       :tags="tags"
                       :allowed="allowed"
                       @getMedia="getMedia"
                       :reorderMode="reorderMode"
                       class="full-width full-height"
          />
        </div>
      </draggable>
      <q-btn v-if="canReorder && !reorderMode" flat icon="swap_vert" :label="$t('contents.reorder')" class="q-mt-sm" @click="startReorder" />
      <q-btn v-if="reorderMode" :loading="reordering" :disable="reordering" flat icon="done" :label="$t('general.confirm')" class="q-mt-sm" color="positive" @click="reorder" />
      <q-btn v-if="reorderMode" flat :label="$t('general.cancel')" class="q-mt-sm" color="grey" @click="cancelReorder" />
    </div>
    <q-dialog v-model="addMediaDialog" @hide="onMediaDialogHide" persistent >
      <q-card style="width: 90vw">
        <q-card-section class="q-pb-none">
          <q-tabs
            v-model="addMediaTab"
            class="text-grey"
            active-color="primary"
            indicator-color="info"
          >
            <q-tab name="upload" :label="$t('media.upload')" />
            <q-tab name="library" :label="$t('media.library')" />
          </q-tabs>
        </q-card-section>
        <q-separator />
        <q-card-section v-if="addMediaTab==='upload'" style="height: 50vh" class="scroll">
          <q-file
            :value="uploadProgress"
            @input="updateFiles"
            :label="$t('media.select')"
            outlined
            multiple
            ref="filePicker"
            class="full-width media-library-file-picker"
            :class="{ 'with-files': this.uploadProgress.length > 0 }"
            :accept="acceptedFiles"
            @rejected="rejectedFiles"
          >
            <template v-slot:file></template>
          </q-file>
          <q-list class="rounded-borders media-library-file-list q-mt-md" v-if="uploadProgress.length > 0">
            <q-item v-for="(fileToUpload, index) in uploadProgress" :key="index" class="q-pa-none q-mb-md">
              <q-item-section>
                <nq-input v-model="fileToUpload.name"
                          :diabled="fileToUpload.uploading || fileToUpload.created"
                          :readonly="fileToUpload.uploading || fileToUpload.created"
                          size="sm" dense class="full-width q-mb-xs">
                  <template v-slot:append>
                    <small>{{ fileToUpload.size | numeral('0a') }}</small>
                  </template>
                  <!--template v-slot:prepend>
                    {{ index + 1 }}
                  </template-->
                </nq-input>
                <q-item-label>
                  <q-linear-progress class="full-width"
                                     :value="fileToUpload.percent"
                                     :color="fileToUpload.color"
                                     :indeterminate="fileToUpload.creating" />
                </q-item-label>
              </q-item-section>
              <q-item-section side>
                <q-btn icon="clear" size="sm" class="q-ml-sm" round flat color="grey" @click="removeFile(index)" v-if="!uploading" />
                <q-circular-progress
                  track-color="grey-3"
                  :value="fileToUpload.uploaded ? 1 : 0"
                  :color="fileToUpload.uploaded ? 'positive' : 'primary'"
                  :indeterminate="fileToUpload.uploading || fileToUpload.creating" size="sm"
                  v-if="uploading" />
              </q-item-section>
            </q-item>
          </q-list>
        </q-card-section>
        <q-separator v-if="addMediaTab==='upload'"/>
        <q-card-actions align="center" v-if="addMediaTab==='upload'">
          <q-btn
            color="grey"
            flat
            :label="$t('general.cancel')"
            v-close-popup
            class="q-mr-md"
          />
          <q-btn
            color="primary"
            icon="cloud_upload"
            :label="$t('media.upload')"
            @click="upload"
            :disable="!canUpload"
            :loading="uploading"
          />
        </q-card-actions>
        <q-card-section v-if="addMediaTab==='library'">
          <div class="text-h6">TBD</div>
        </q-card-section>
      </q-card>
    </q-dialog>
  </div>
</template>
<script>
import draggable from 'vuedraggable'
import _ from 'lodash'
import MediumItem from './MediumItem'
export default {
  components: { MediumItem, draggable },
  name: 'Media',
  props: {
    entity: {},
    readonly: {
      type: Boolean,
      default: true
    },
    allowed: {
      type: Array,
      default: () => []
    },
    tags: {
      type: Array,
      default: () => []
    }
  },
  data () {
    return {
      loading: true,
      media: [],
      storedMedia: [],
      reorderMode: false,
      reordering: false,
      addMediaDialog: false,
      addMediaTab: 'upload',
      uploadProgress: [],
      uploading: false
    }
  },
  mounted () {
    this.getMedia()
  },
  methods: {
    async getMedia () {
      this.loading = true
      const mediaResult = await this.$api.get(`/entities/medium?media-of=${this.entity.id}&select=contents.title,properties,is_active,model,id,relation_media.relation_id&only-published=false&order-by=relation_media.position&per-page=100`)
      this.loading = false
      if (mediaResult.success) {
        this.media = mediaResult.data.data
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
      this.storedMedia = _.cloneDeep(this.media)
      this.reorderMode = true
    },
    cancelReorder () {
      this.media = _.cloneDeep(this.storedMedia)
      this.reorderMode = false
    },
    async reorder () {
      this.reordering = true
      const relation_ids = _.flatMap(this.media, (c) => c.relation_id)
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
    updateFiles (files) {
      for (const f in files) {
        if (!_.find(this.uploadProgress, o => (o.file.name === files[f].name && o.file.lastModified === files[f].lastModified))) {
          let name = files[f].name
          const dot = name.lastIndexOf('.')
          if (dot > -1) {
            name = name.substr('0', dot)
          }
          name = name.replace('-', ' ')
          name = name.replace('_', ' ')
          this.uploadProgress.push({
            createError: false,
            uploadError: false,
            size: files[f].size,
            name: name,
            percent: 0,
            indeterminate: false,
            creating: false,
            created: false,
            uploading: false,
            uploaded: false,
            tags: [],
            file: files[f],
            color: 'primary'
          })
        }
      }
      this.$refs.filePicker.blur()
    },
    removeFile (index) {
      this.uploadProgress.splice(index, 1)
    },
    async upload () {
      this.uploading = true
      let allRight = true
      for (const f in this.uploadProgress) {
        let createAndRelateResult = {}
        if (!this.uploadProgress[f].created) {
          this.uploadProgress[f].creating = true
          const titles = {}
          // TODO: Allow the user to set different titles for each language
          for (const l in this.$store.state.ui.config.langs) {
            titles[this.$store.state.ui.config.langs[l]] = this.uploadProgress[f].name
          }
          createAndRelateResult = await this.$api.post(`/entity/${this.entity.id}/create_and_relate`, {
            model: 'medium',
            kind: 'medium',
            position: Number(this.media.length) + Number(f),
            tags: this.uploadProgress[f].tags,
            contents: { title: titles }
          })
          this.uploadProgress[f].creating = false
          this.uploadProgress[f].id = createAndRelateResult.data.id
        } else {
          createAndRelateResult.success = true
        }
        if (createAndRelateResult.success) {
          this.uploadProgress[f].createError = false
          this.uploadProgress[f].created = true
          let uploadResult = {}
          if (!this.uploadProgress[f].uploaded) {
            this.uploadProgress[f].uploading = true
            this.uploadProgress[f].color = 'primary'
            this.uploadProgress[f].percent = 0
            const data = new FormData()
            data.append('file', this.uploadProgress[f].file)
            uploadResult = await this.$api.post(`/medium/${this.uploadProgress[f].id}/upload`, data, { 'Content-Type': 'multipart/form-data' })
          } else {
            uploadResult.success = true
          }
          this.uploadProgress[f].uploading = false
          if (uploadResult.success) {
            this.uploadProgress[f].uploadError = false
            this.uploadProgress[f].uploaded = true
            this.uploadProgress[f].percent = 1
            this.uploadProgress[f].color = 'positive'
          } else {
            allRight = false
            this.uploadProgress[f].uploadError = true
            this.uploadProgress[f].percent = 1
            this.uploadProgress[f].color = 'negative'
          }
        } else {
          allRight = false
          this.uploadProgress[f].createError = true
          this.uploadProgress[f].percent = 0
          this.uploadProgress[f].color = 'negative'
        }
      }
      if (allRight) {
        this.getMedia()
        this.onMediaDialogHide()
        this.addMediaDialog = false
      }
      this.uploading = false
    },
    onMediaDialogHide () {
      this.uploadProgress = []
    },
    rejectedFiles (files) {
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
    canReorder () {
      return this.media.length > 1
    },
    canUpload () {
      return this.uploadProgress.length > 0
    },
    acceptedFiles () {
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
