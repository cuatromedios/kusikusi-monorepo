<template>
  <q-form>
    <nq-page title="Media" max-width="lg">
      <h1>&nbsp;</h1>
      <q-card>
        <q-card-section>
          <media-library/>
        </q-card-section>
      </q-card>
    </nq-page>
  </q-form>
</template>

<script>
import draggable from 'vuedraggable'
import MediaLibrary from '../components/MediaLibrary'
export default {
  name: 'Media',
  components: { MediaLibrary },
  data () {
    return {}
  },
  async created () {
  },
  async beforeRouteUpdate (to, from, next) {
  },
  methods: {
  }
}
</script>
<style lang="scss">
</style>
