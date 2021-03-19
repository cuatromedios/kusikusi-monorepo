/**
 * Base mixin for field components.
 */
export const nqFieldMixin = {
  data () {
    return {
      focused: false
    }
  },
  methods: {
    onFocus () {
      this.focused = true
    },
    onBlur () {
      this.focused = false
    }
  }
}
