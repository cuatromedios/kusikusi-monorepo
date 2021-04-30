<template>
  <nq-field class="html-editor" :label="label" :readonly="readonly" stack-label>
    <div class="full-width">
      <editor-menu-bar :editor="editor"
                       v-slot="{ commands, isActive, getMarkAttrs }"
                       v-if="!editingSource"
                       style="position: sticky; top: 64px; z-index: 100">
        <div class="editor-menu-bar" v-show="!readonly">
          <q-btn-group v-for="(group, groupIndex) in toolbar"
                       :key="groupIndex"
                       class="q-mr-sm q-mb-sm bg-white">
            <q-btn v-for="(action, actionIndex) in group" :key="actionIndex"
                   size="sm" flat round
                   :color="action.color ? action.color : isActive[action.activeCommand || action.command](action.dontSendParamsToActiveCommand ? {} : action.params) ? 'info' : 'dark'"
                   v-show="action.showCommand ? isActive[action.showCommand]() : true"
                   :icon="action.icon"
                   :label="action.label"
                   @click="action.method ? methodBridge(action.method, commands[action.command], getMarkAttrs(action.command)) : action.command ? commands[action.command](action.params) : null" />
          </q-btn-group>
        </div>
      </editor-menu-bar>
        <div class="editor-content-container" v-if="!editingSource || readonly">
          <editor-content class="editor-content" :editor="editor" />
        </div>
      <div v-if="!readonly">
        <q-input type="textarea" v-model="sourceCode" class="full-width q-mt-sm" v-if="editingSource"></q-input>
        <q-btn :flat="!editingSource" :outline="editingSource" size="sm" class="q-my-xs" @click="editingSource = !editingSource" :icon="editingSource ? 'close' : ''">Edit source</q-btn>
      </div>
    </div>
  </nq-field>
</template>
<script>
import { Editor, EditorContent, EditorMenuBar } from 'tiptap'
import { Heading, CodeBlock, Bold, Italic, Strike, Underline, Code, BulletList, OrderedList, Blockquote, ListItem, Table, TableHeader, TableCell, TableRow, History, Link, HardBreak } from 'tiptap-extensions'
export default {
  name: 'HtmlEdior',
  components: {
    EditorContent,
    EditorMenuBar
  },
  mounted () {
    this.editor = new Editor({
      content: this.value,
      editable: !this.readonly,
      extensions: [
        new Heading({ levels: [1, 2, 3, 4, 5, 6] }),
        new Bold(),
        new Italic(),
        new Strike(),
        new Underline(),
        new CodeBlock(),
        new Code(),
        new BulletList(),
        new OrderedList(),
        new ListItem(),
        new Blockquote(),
        new Table(),
        new TableHeader(),
        new TableCell(),
        new TableRow(),
        new History(),
        new HardBreak(),
        new Link({ openOnClick: false })
      ],
      onUpdate: ({ getHTML }) => {
        this.contentUpdated(getHTML())
      }
    })
  },
  props: {
    value: String,
    label: String,
    readonly: {
      type: Boolean,
      default: true
    },
    toolbar: {
      type: Array,
      default: () => [
        [
          { command: 'paragraph', label: 'P', params: {} },
          { command: 'heading', label: 'H1', params: { level: 1 } },
          { command: 'heading', label: 'H2', params: { level: 2 } },
          { command: 'heading', label: 'H3', params: { level: 3 } },
          { command: 'heading', label: 'H4', params: { level: 4 } },
          { command: 'heading', label: 'H5', params: { level: 5 } },
          { command: 'heading', label: 'H6', params: { level: 6 } },
          { command: 'code_block', icon: 'code' }
        ],
        [
          { command: 'bold', icon: 'format_bold' },
          { command: 'italic', icon: 'format_italic' },
          { command: 'strike', icon: 'format_strikethrough' },
          { command: 'underline', icon: 'format_underline' },
          { command: 'code', icon: 'functions' }
        ],
        [
          { method: 'addLink', command: 'link', activeCommand: 'link', icon: 'link' }
        ],
        [
          { command: 'bullet_list', icon: 'format_list_bulleted' },
          { command: 'ordered_list', icon: 'format_list_numbered' },
          { command: 'blockquote', icon: 'format_quote' }
        ],
        [
          { command: 'createTable', activeCommand: 'table', icon: 'border_all', params: { rowsCount: 3, colsCount: 3, withHeaderRow: true }, dontSendParamsToActiveCommand: true },
          { command: 'deleteTable', activeCommand: 'table', showCommand: 'table', icon: 'border_all', color: 'negative' },
          { command: 'addColumnBefore', activeCommand: 'table', showCommand: 'table', icon: 'border_left', color: 'positive' },
          { command: 'addColumnAfter', activeCommand: 'table', showCommand: 'table', icon: 'border_right', color: 'positive' },
          { command: 'deleteColumn', activeCommand: 'table', showCommand: 'table', icon: 'border_vertical', color: 'negative' },
          { command: 'addRowBefore', activeCommand: 'table', showCommand: 'table', icon: 'border_top', color: 'positive' },
          { command: 'addRowAfter', activeCommand: 'table', showCommand: 'table', icon: 'border_bottom', color: 'positive' },
          { command: 'deleteRow', activeCommand: 'table', showCommand: 'table', icon: 'border_horizontal', color: 'negative' },
          { command: 'toggleCellMerge', activeCommand: 'table', showCommand: 'table', icon: 'border_outer', color: 'dark' }
        ]
      ]
    }
  },
  data () {
    return {
      editor: null,
      editingSource: false
    }
  },
  methods: {
    methodBridge (method, command, attributes) {
      this[method](command, attributes)
    },
    addLink (command, attributes) {
      this.$q.dialog({
        title: 'Link address',
        prompt: {
          model: attributes.href || 'https://',
          type: 'text'
        },
        cancel: true,
        persistent: true
      }).onOk(data => {
        command({ href: data })
      }).onCancel(() => {
        // console.log('>>>> Cancel')
      }).onDismiss(() => {
        // console.log('I am triggered on both OK and Cancel')
      })
    },
    contentUpdated (html) {
      this.$emit('input', html)
    }
  },
  computed: {
    sourceCode: {
      get () {
        return this.value
      },
      set (val) {
        this.editor.setContent(val)
        this.$emit('input', val)
      }
    }
  },
  watch: {
    readonly () {
      this.editor.setOptions({
        editable: !this.readonly
      })
    }
  },
  beforeDestroy () {
    this.editor.destroy()
  }
}
</script>
<style lang="scss">
  .html-editor {
    .editor-menu-bar {
      margin: 0.5em 0;
      .q-btn__content div {
        font-size: 13px;
        font-weight: bold;
      }
    }
    .editor-content-container {
      background-color: rgba(255,255,255,0.75);
      margin: 2px -10px;
      padding: 1px 12px;
    }
    .editor-content {
      article,aside,details,figcaption,figure,footer,header,hgroup,nav,section,summary{display:block;}audio,canvas,video{display:inline-block;*display:inline;*zoom:1;}audio:not([controls]){display:none;height:0;}[hidden]{display:none;}html{font-size:100%;-webkit-text-size-adjust:100%;-ms-text-size-adjust:100%;}html,button,input,select,textarea{font-family:sans-serif;}body{margin:0;}a:focus{outline:thin dotted;}a:active,a:hover{outline:0;}h1{font-size:2em;margin:0.67em 0;}h2{font-size:1.5em;margin:0.83em 0;}h3{font-size:1.17em;margin:1em 0;}h4{font-size:1em;margin:1.33em 0;}h5{font-size:0.83em;margin:1.67em 0;}h6{font-size:0.75em;margin:2.33em 0;}abbr[title]{border-bottom:1px dotted;}b,strong{font-weight:bold;}blockquote{margin:1em 40px;}dfn{font-style:italic;}mark{background:#ff0;color:#000;}p,pre{margin:1em 0;}code,kbd,pre,samp{font-family:monospace,serif;_font-family:'courier new',monospace;font-size:1em;}pre{white-space:pre;white-space:pre-wrap;word-wrap:break-word;}q{quotes:none;}q:before,q:after{content:'';content:none;}small{font-size:75%;}sub,sup{font-size:75%;line-height:0;position:relative;vertical-align:baseline;}sup{top:-0.5em;}sub{bottom:-0.25em;}dl,menu,ol,ul{margin:1em 0;}dd{margin:0 0 0 40px;}menu,ol,ul{padding:0 0 0 40px;}nav ul,nav ol{list-style:none;list-style-image:none;}img{border:0;-ms-interpolation-mode:bicubic;}svg:not(:root){overflow:hidden;}figure{margin:0;}form{margin:0;}fieldset{border:1px solid #c0c0c0;margin:0 2px;padding:0.35em 0.625em 0.75em;}legend{border:0;padding:0;white-space:normal;*margin-left:-7px;}button,input,select,textarea{font-size:100%;margin:0;vertical-align:baseline;*vertical-align:middle;}button,input{line-height:normal;}button,html input[type="button"],input[type="reset"],input[type="submit"]{-webkit-appearance:button;cursor:pointer;*overflow:visible;}button[disabled],input[disabled]{cursor:default;}input[type="checkbox"],input[type="radio"]{box-sizing:border-box;padding:0;*height:13px;*width:13px;}input[type="search"]{-webkit-appearance:textfield;-moz-box-sizing:content-box;-webkit-box-sizing:content-box;box-sizing:content-box;}input[type="search"]::-webkit-search-cancel-button,input[type="search"]::-webkit-search-decoration{-webkit-appearance:none;}button::-moz-focus-inner,input::-moz-focus-inner{border:0;padding:0;}textarea{overflow:auto;vertical-align:top;}table{border-collapse:collapse;border-spacing:0;}
    }
    .editor-content {
      margin: 1em 0;
      &:focus {
        outline: none;
      }
      .ProseMirror:focus {
        outline: none;
      }
      * {
        color: $dark;
      }

      h1, h2, h3, p {
        color: $dark;
        font-size: 1.25em;
        margin-bottom: 0.5em;
        letter-spacing: normal;
        text-transform: none;
      }
      pre {
        background-color: lightgrey;
        padding: 0.5em;
        border: 1px solid grey;
        border-radius: 0.25em;
        color: darkgreen;
      }
      blockquote {
        margin: 1em 0 1em 0em;
        padding-left: 1.5em;
        border-left: 0.5em solid lightgrey;
      }
      h1 {
        font-size: 1.75em;
        font-weight: bold;
      }
      h2 {
        font-size: 1.5em;
        font-weight: bold;
      }
      h3 {
        font-size: 1.25em;
        font-weight: bold;
      }
      u {
        text-decoration: underline;
      }
      strong {
        font-weight: bold;
      }
      em {
        font-style: italic;
      }
      s {
        text-decoration: line-through;
      }
      table th {
        background-color: #e0e0e0;
      }
      table td, table th {
        border: 1px dotted grey;
        padding: 0.5em 1em;
        &.selectedCell {
          outline: 2px dotted $info;
          outline-offset: -1px;
          cursor: cell;
        }
        p {
          margin: 0
        }
      }
      li {
        p {
          margin: 0
        }
      }
      a {
        position: relative;
      }
      a:hover::before {
        content: attr(href);
        display: block;
        position: absolute;
        background-color: #f8f8f8;
        border: 1px solid #e8e8e8;
        top: 1.5em;
        left: 0;
        padding: .25em 1em;
        border-radius: .5em;
        font-size: 12px;
        font-weight: normal;
        color: #444;
      }
    }
  }

</style>
