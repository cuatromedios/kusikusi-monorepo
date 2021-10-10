<template>
  <q-form @submit="save">
    <nq-page :title="getEntityTitle" max-width="lg">
      <template slot="before" v-if="ancestors.length > 0">
        <q-breadcrumbs>
          <q-breadcrumbs-el v-for="ancestor in ancestors"
                            :key="ancestor.id"
                            :label="ancestor.content.title || (ancestor.properties ? ancestor.properties.title : null) || '-'"
                            :to="{ name: 'content', params: { entity_id: ancestor.id } }"
          />
          <q-breadcrumbs-el />
        </q-breadcrumbs>
      </template>
      <div id="langSelector" v-if="$store.state.ui.config && $store.state.ui.config.langs.length > 1" class="rounded-borders shadow-1">
        <q-radio v-model="contentLang"
                 size="sm"
                 v-for="lang in $store.state.ui.config.langs"
                 :key="lang"
                 :val="lang"
                 :label="$t(lang)"
        />
        <q-radio v-model="contentLang"
                 val="all"
                 :label="$t('all')"/>
      </div>
      <template slot="aside">
        <h2>{{ $t('contents.publication') }}</h2>
        <q-card>
          <q-card-section v-if="!loading">
            <div class="row q-col-gutter-sm">
              <nq-field dense class="col-12" :readonly="!editing" v-if="editing" >
                <q-checkbox v-model="entity.is_active" :label="$t('contents.active')" left-label color="dark" />
              </nq-field>
              <div class="text-center col-12 text-positive" v-if="!editing && entity.is_active">{{ $t('contents.active') }}</div>
              <div class="text-center col-12 text-warning" v-if="!editing && !entity.is_active">{{ $t('contents.notActive') }}</div>
              <nq-select dense v-model="entity.view" :label="$t('contents.view')" class="col-12" :readonly="!editing" :options="views"/>
              <nq-date-time dense v-model="entity.published_at" display-format="dddd DD MMMM YYYY, h:mm a" :label="$t('contents.publishedAt')" class="col-12" :readonly="!editing" v-if="editing"></nq-date-time>
              <nq-field :label="$t('contents.publishedAt')" class="col-12" readonly stack-label v-if="!editing">
                <p :class=" { 'text-warning': !isBefore(entity.published_at) } ">{{ entity.published_at | moment('dddd DD MMMM YYYY, h:mm a') }} {{ isBefore(entity.published_at) ? "S" : "N" }}
                  <small>(UTC&nbsp;{{ entity.published_at | moment('Z') }})</small></p>
              </nq-field>
              <nq-input label="ID" class="col-12" :readonly="!isNew" stack-label v-model="entity.id" :rules="[$rules.required(), $rules.minLength(3), $rules.maxLength(16), val => RegExp('^[A-Za-z0-9_-]{3,16}$').test(val)]" />
              <q-btn class="col-8 offset-2" flat size="sm" @click="clearCache" :loading="clearing">{{ $t('contents.clearCache') }}</q-btn>
            </div>
          </q-card-section>
          <q-card-section v-if="loading">
            <q-skeleton type="QSlider" class="q-mb-md" />
            <q-skeleton type="QSlider" class="q-mb-md" />
            <q-skeleton type="QSlider" class="q-mb-md" />
            <q-skeleton type="QSlider" class="q-mb-md" />
          </q-card-section>
        </q-card>
        <div class="flex flex-center q-mt-md">
          <q-btn size="xs" color="negative" flat @click="onDelete">{{ $t('general.delete')}}</q-btn>
        </div>
      </template>
      <template v-if="loading">
        <h2>&nbsp;</h2>
        <q-card>
          <q-card-section v-if="loading">
            <q-skeleton type="QSlider" class="q-mb-md" />
            <q-skeleton type="QSlider" class="q-mb-md" />
            <q-skeleton type="QSlider" class="q-mb-md" />
            <q-skeleton type="QSlider" class="q-mb-md" />
          </q-card-section>
        </q-card>
      </template>
      <template v-if="!loading">
        <div v-for="(fieldset, fieldsetIndex) in fieldsets" :key="fieldsetIndex">
          <h2>{{ $t(fieldset.label) }}</h2>
          <q-card>
            <q-card-section>
              <div class="row q-col-gutter-md">
                <div v-for="(component, componentIndex) in fieldset.components"
                     :key="componentIndex"
                     :is="component.component"
                     :label="`${$t(component.label)} ${component.isMultiLang ? '('+component.props.lang+')' : ''}`"
                     :readonly="!editing"
                     :data-multilingual="component.isMultiLang"
                     :data-lang="component.props ? component.props.lang : ''"
                     :lang="component.props ? component.props.lang : null"
                     :entity="entity"
                     v-show="!component.isMultiLang || (component.isMultiLang && (component.props.lang === contentLang || contentLang === 'all'))"
                     :value="getValue(component)"
                     @input="setValue(component, $event)"
                     v-bind="component.props"
                     :rules="getRules(component)"
                     class="col-12" />
              </div>
            </q-card-section>
          </q-card>
        </div>
      </template>
      <div class="fixed-top-right text-white action-buttons">
        <q-btn v-if="!editing && !loading && isEditable"
               push size="lg" icon="edit" class="bg-accent no-border-radius"
               :label="$t('general.edit')"
               type="a"
               :disable="saving"
               @click="editing = true" />
        <q-btn v-if="editing"
               type="a"
               flat size="md" class="no-border-radius q-mr-sm"
               :label="$t('general.cancel')"
               @click="cancel" />
        <q-btn v-if="editing"
               push size="lg" icon="cloud_upload" class="bg-positive no-border-radius"
               :disable="loading || saving"
               :loading="saving"
               type="submit"
               :label="$t('general.save')" />
      </div>
    </nq-page>
  </q-form>
</template>

<script>
import _ from 'lodash'
import moment from 'moment'
import Children from '../components/Children'
import Media from '../components/Media'
import Relations from '../components/Relations'
import RelationsReceived from '../components/RelationsReceived'
import HtmlEditor from '../components/HtmlEditor'
import Slug from '../components/Slug'
export default {
  name: 'Content',
  components: { Children, Relations, RelationsReceived, Media, HtmlEditor, Slug },
  data () {
    return {
      editing: false,
      loading: true,
      saving: false,
      fieldsets: [],
      ancestors: [],
      clearing: false,
      entity: {
        id: '',
        model: '',
        view: '',
        contents: [],
        properties: { title: '' },
        entity_relations: [],
        parent_entity_id: '',
        is_active: true,
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
  },
  async created () {
    await this.refreshEntity(this.$route.params.entity_id)
  },
  async beforeRouteUpdate (to, from, next) {
    await this.refreshEntity(to.params.entity_id, to.params.model, to.params.parent_entity_id)
    next()
  },
  methods: {
    async refreshEntity (entity_id, model, parent_entity_id) {
      this.loading = true
      this.editing = false
      this.saving = false
      this.fieldsets = []
      this.$store.commit('setEditButton', true)
      this.$store.commit('setSaveButton', false)
      this.entity.id = entity_id || 'home'
      if (entity_id !== 'new') {
        const contentResult = await this.$api.get(`/entities/${this.entity.id}?with=contents,entities_related`)
        this.loading = false
        if (contentResult.success) {
          this.entity = contentResult.data
          const ancestorsResult = await this.$api.get(`/entities?ancestors-of=${this.entity.id}&descendant-of=website&select=id,content.title,properties&order-by=ancestor.depth:DESC`)
          if (ancestorsResult.success) {
            this.ancestors = ancestorsResult.data.data
          } else {
            this.ancestors = []
          }
        } else {
          this.entity = {
            contents: [],
            entity_relations: []
          }
        }
      } else {
        this.entity = _.cloneDeep(this.$store.state.content.blankEntity)
        this.entity.model = model || this.$route.params.model
        this.entity.view = model || this.$route.params.model
        this.entity.id = entity_id || this.$route.params.entity_id
        this.entity.parent_entity_id = parent_entity_id || this.$route.params.parent_entity_id
        this.entity.published_at = moment().format()
        this.entity.unpublished_at = null
        this.editing = true
        this.$nextTick(() => {
          this.loading = false
        })
      }
      this.fieldsets = _.get(this.$store.state, `ui.config.models.${String(this.entity.model).toLowerCase()}.form`, [])
    },
    async clearCache () {
      this.clearing = true
      const result = await this.$api.delete(`/static/web${this.entity.id !== 'website' ? `/${this.entity.id}` : ''}`)
      if (result.success) {
        this.$q.notify({
          position: 'top',
          color: 'positive',
          message: this.$t('contents.cacheCleared')
        })
      } else {
        this.$q.notify({
          position: 'top',
          color: 'negative',
          message: this.$t('general.serverError')
        })
      }
      this.clearing = false
    },
    getValue (component) {
      if (component.isMultiLang) {
        const foundField = this.findContentRow(component.props)
        if (foundField) {
          return foundField.text
        } else {
          this.entity.contents.push({
            lang: component.props.lang,
            field: component.props.field,
            text: ''
          })
        }
      } else if (component.value && component.value.startsWith('properties.')) {
        const fieldName = component.value.split('.')[1]
        return this.entity.properties[fieldName]
      } else {
        return this.entity[component.value]
      }
    },
    setValue (component, value) {
      if (component.isMultiLang) {
        const foundField = this.findContentRow(component.props)
        if (foundField) {
          foundField.text = value
        }
      } else if (component.value && component.value.startsWith('properties.')) {
        const fieldName = component.value.split('.')[1]
        this.$set(this.entity.properties, fieldName, value)
      } else {
        this.entity[component.value] = value
      }
    },
    getRules (component) {
      const rules = []
      if (component.rules) {
        for (const r in component.rules) {
          rules.push(this.$rules[component.rules[r][0]](component.rules[r][1], component.rules[r][2], component.rules[r][3]))
        }
      }
      return rules
    },
    findContentRow (props) {
      if (!this.entity) return undefined
      return _.find(this.entity.contents, (o) => { return o.lang === props.lang && o.field === props.field })
    },
    cancel () {
      if (this.isNew) {
        this.$router.replace({ name: 'content', params: { entity_id: this.$route.params.parent_entity_id } })
      } else {
        this.refreshEntity(this.entity.id)
      }
    },
    async save () {
      this.saving = true
      let saveResult
      if (this.isNew) {
        if (this.entity.id === 'new') delete this.entity.id
        saveResult = await this.$api.post('/entities', this.entity)
      } else {
        saveResult = await this.$api.patch(`/entities/${this.entity.id}`, this.entity)
      }
      this.saving = false
      if (saveResult.success) {
        this.$q.notify({
          position: 'top',
          color: 'positive',
          message: this.$t('contents.saveOk')
        })
        this.editing = false
        if (this.isNew) {
          this.$router.replace({ name: 'content', params: { entity_id: saveResult.data.id } })
        }
      } else {
        this.$q.notify({
          position: 'top',
          color: 'negative',
          message: this.$t('general.saveError'),
          caption: `${saveResult.data.message} (${saveResult.status})`
        })
      }
    },
    onDelete () {
      this.$q.dialog({
        title: this.$t('general.delete'),
        ok: {
          label: this.$t('general.delete'),
          color: 'negative'
        },
        cancel: {
          label: this.$t('general.cancel'),
          color: 'grey',
          flat: true
        },
        message: this.$t('general.sure')
      }).onOk(async () => {
        await this.$api.delete(`/entities/${this.entity.id}`)
        this.$router.back()
      })
    },
    isBefore (date1, date2) {
      if (!date2) date2 = moment()
      return moment(date1).isBefore(date2)
    }
  },
  computed: {
    getEntityTitle () {
      if (!this.entity) return ''
      const fromContents = this.findContentRow({ lang: this.$store.state.ui.config.langs[0], field: 'title' })
      return fromContents && fromContents.text !== '' ? fromContents.text : this.entity.properties ? this.entity.properties.title || this.$t(this.$store.getters.nameOf(this.entity.model)) : ''
    },
    isNew () {
      return this.$route.params.entity_id === 'new'
    },
    isEditable () {
      return _.get(this.$store.state, `ui.config.models.${this.entity.model}.editable`, true)
    },
    views () {
      return _.get(this.$store.state, `ui.config.models.${this.entity.model}.views`, [this.entity.model])
    },
    contentLang: {
      set (lang) {
        this.$store.commit('setLang', lang)
      },
      get () {
        return this.$store.state.ui.lang
      }
    }
  }
}
</script>
<style lang="scss">
  .action-buttons {
    z-index: 2001;
    .q-btn {
       max-height: 50px
    }
  }
  #langSelector {
    font-size: 0.85em;
    position: fixed;
    top: 64px;
    right: 24px;
    background-color: rgba(255,255,255,0.5);
    padding: 4px 16px;
  }
</style>
