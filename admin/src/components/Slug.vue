<template>
  <div class="flex justify-between">
    <nq-input v-model="computedValue" style="flex: 1" :readonly="readonly || auto" :label="label" />
    {{ entity.title }}
    <q-checkbox v-model="auto" label="Auto" v-if="!readonly" @input="changeAuto" />
  </div>
</template>
<script>
import slugify from 'slugify'
export default {
  props: {
    value: {
      type: String,
      default: ''
    },
    readonly: {
      type: Boolean,
      default: true
    },
    label: {
      type: String,
      default: 'Slug'
    },
    entity: {
      type: Object,
      default: () => ({})
    },
    lang: {
      type: String,
      default: null
    },
    fromField: {
      type: String,
      default: 'contents.title'
    }
  },
  data () {
    return {
      auto: false,
      referenceField: { type: null, object: null, prop: null },
      referencedValue: ''
    }
  },
  mounted () {
    this.refresh()
  },
  beforeRouteUpdate (to, from, next) {
    this.refresh()
    next()
  },
  computed: {
    valueReference () {
      if (this.referenceField.type === null) return null
      return this.referenceField.object[this.referenceField.prop]
    },
    computedValue: {
      get () {
        return this.value
      },
      set (val) {
        this.$emit('input', val)
      }
    }
  },
  methods: {
    refresh () {
      if (this.entity.id === 'new') {
        this.auto = true
      }
      const parts = this.fromField.split('.')
      if (parts.length === 1) {
        this.referenceField = {
          type: 'entity',
          object: this.entity,
          prop: parts[0]
        }
      } else if (parts[0] === 'contents') {
        for (const f in this.entity.contents) {
          if (this.entity.contents[f].field === parts[1] && this.entity.contents[f].lang === this.lang) {
            this.referenceField = {
              type: 'content',
              object: this.entity.contents[f],
              prop: 'text'
            }
          }
        }
      } else if (parts[0] === 'properties') {
        this.referenceField = this.entity.properties
        this.referenceField = {
          type: 'properties',
          object: this.entity.properties,
          prop: parts[1]
        }
      }
    },
    calculateSlug () {
      this.$emit('input', slugify(this.referencedValue, { lower: true, strict: true }))
    },
    changeAuto () {
      if (this.auto) {
        this.calculateSlug()
      }
    }
  },
  watch: {
    valueReference (val) {
      this.referencedValue = val
      if (this.auto) {
        this.calculateSlug()
      }
    }
  }
}
</script>
