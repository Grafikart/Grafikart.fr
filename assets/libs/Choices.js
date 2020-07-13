// En attendant le support de choices.js en module ES https://cdn.pika.dev/choices.js@%5E9.0.1

function unwrapExports (x) {
  return x && x.__esModule && Object.prototype.hasOwnProperty.call(x, 'default') ? x.default : x
}

function createCommonjsModule (fn, module) {
  return (module = { exports: {} }), fn(module, module.exports), module.exports
}

let choices = createCommonjsModule((module, exports) => {
  /*! choices.js v9.0.1 | © 2019 Josh Johnson | https://github.com/jshjohnson/Choices#readme */
  (function webpackUniversalModuleDefinition (root, factory) {
    module.exports = factory()
  })(window, () => {
    return /******/ (function (modules) {
      // webpackBootstrap
      /******/ // The module cache
      /******/ let installedModules = {} // The require function
      /******/
      /******/ /******/ function __webpack_require__ (moduleId) {
        /******/
        /******/ // Check if module is in cache
        /******/ if (installedModules[moduleId]) {
          /******/ return installedModules[moduleId].exports
          /******/
        } // Create a new module (and put it into the cache)
        /******/ /******/ let module = (installedModules[moduleId] = {
          /******/ i: moduleId,
          /******/ l: false,
          /******/ exports: {}
          /******/
        }) // Execute the module function
        /******/
        /******/ /******/ modules[moduleId].call(module.exports, module, module.exports, __webpack_require__) // Flag the module as loaded
        /******/
        /******/ /******/ module.l = true // Return the exports of the module
        /******/
        /******/ /******/ return module.exports
        /******/
      } // expose the modules object (__webpack_modules__)
      /******/
      /******/
      /******/ /******/ __webpack_require__.m = modules // expose the module cache
      /******/
      /******/ /******/ __webpack_require__.c = installedModules // define getter function for harmony exports
      /******/
      /******/ /******/ __webpack_require__.d = function (exports, name, getter) {
        /******/ if (!__webpack_require__.o(exports, name)) {
          /******/ Object.defineProperty(exports, name, { enumerable: true, get: getter })
          /******/
        }
        /******/
      } // define __esModule on exports
      /******/
      /******/ /******/ __webpack_require__.r = function (exports) {
        /******/ if (typeof Symbol !== 'undefined' && Symbol.toStringTag) {
          /******/ Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' })
          /******/
        }
        /******/ Object.defineProperty(exports, '__esModule', { value: true })
        /******/
      } // create a fake namespace object // mode & 1: value is a module id, require it // mode & 2: merge all properties of value into the ns // mode & 4: return value when already ns object // mode & 8|1: behave like require
      /******/
      /******/ /******/ /******/ /******/ /******/ /******/ __webpack_require__.t = function (value, mode) {
        /******/ if (mode & 1) value = __webpack_require__(value)
        /******/ if (mode & 8) return value
        /******/ if (mode & 4 && typeof value === 'object' && value && value.__esModule) return value
        /******/ let ns = Object.create(null)
        /******/ __webpack_require__.r(ns)
        /******/ Object.defineProperty(ns, 'default', { enumerable: true, value })
        /******/ if (mode & 2 && typeof value !== 'string')
          for (let key in value)
            __webpack_require__.d(
              ns,
              key,
              ((key) => {
                return value[key]
              }).bind(null, key)
            )
        /******/ return ns
        /******/
      } // getDefaultExport function for compatibility with non-harmony modules
      /******/
      /******/ /******/ __webpack_require__.n = function (module) {
        /******/ let getter =
          module && module.__esModule
            ? /******/ function getDefault () {
                return module.default
              }
            : /******/ function getModuleExports () {
                return module
              }
        /******/ __webpack_require__.d(getter, 'a', getter)
        /******/ return getter
        /******/
      } // Object.prototype.hasOwnProperty.call
      /******/
      /******/ /******/ __webpack_require__.o = function (object, property) {
        return Object.prototype.hasOwnProperty.call(object, property)
      } // __webpack_public_path__
      /******/
      /******/ /******/ __webpack_require__.p = '/public/assets/scripts/' // Load entry module and return exports
      /******/
      /******/
      /******/ /******/ return __webpack_require__((__webpack_require__.s = 4))
      /******/
    })(
      /************************************************************************/
      /******/ [
        /* 0 */
        /***/ function (module, exports, __webpack_require__) {
          let isMergeableObject = function isMergeableObject (value) {
            return isNonNullObject(value) && !isSpecial(value)
          }

          function isNonNullObject (value) {
            return !!value && typeof value === 'object'
          }

          function isSpecial (value) {
            let stringValue = Object.prototype.toString.call(value)

            return stringValue === '[object RegExp]' || stringValue === '[object Date]' || isReactElement(value)
          }

          // see https://github.com/facebook/react/blob/b5ac963fb791d1298e7f396236383bc955f916c1/src/isomorphic/classic/element/ReactElement.js#L21-L25
          let canUseSymbol = typeof Symbol === 'function' && Symbol.for
          let REACT_ELEMENT_TYPE = canUseSymbol ? Symbol.for('react.element') : 0xeac7

          function isReactElement (value) {
            return value.$$typeof === REACT_ELEMENT_TYPE
          }

          function emptyTarget (val) {
            return Array.isArray(val) ? [] : {}
          }

          function cloneUnlessOtherwiseSpecified (value, options) {
            return options.clone !== false && options.isMergeableObject(value)
              ? deepmerge(emptyTarget(value), value, options)
              : value
          }

          function defaultArrayMerge (target, source, options) {
            return target.concat(source).map((element) => {
              return cloneUnlessOtherwiseSpecified(element, options)
            })
          }

          function getMergeFunction (key, options) {
            if (!options.customMerge) {
              return deepmerge
            }
            let customMerge = options.customMerge(key)
            return typeof customMerge === 'function' ? customMerge : deepmerge
          }

          function getEnumerableOwnPropertySymbols (target) {
            return Object.getOwnPropertySymbols
              ? Object.getOwnPropertySymbols(target).filter((symbol) => {
                  return target.propertyIsEnumerable(symbol)
                })
              : []
          }

          function getKeys (target) {
            return Object.keys(target).concat(getEnumerableOwnPropertySymbols(target))
          }

          // Protects from prototype poisoning and unexpected merging up the prototype chain.
          function propertyIsUnsafe (target, key) {
            try {
              return (
                key in target && // Properties are safe to merge if they don't exist in the target yet,
                !(
                  (Object.hasOwnProperty.call(target, key) && Object.propertyIsEnumerable.call(target, key)) // unsafe if they exist up the prototype chain,
                )
              ) // and also unsafe if they're nonenumerable.
            } catch (unused) {
              // Counterintuitively, it's safe to merge any property on a target that causes the `in` operator to throw.
              // This happens when trying to copy an object in the source over a plain string in the target.
              return false
            }
          }

          function mergeObject (target, source, options) {
            let destination = {}
            if (options.isMergeableObject(target)) {
              getKeys(target).forEach((key) => {
                destination[key] = cloneUnlessOtherwiseSpecified(target[key], options)
              })
            }
            getKeys(source).forEach((key) => {
              if (propertyIsUnsafe(target, key)) {
                return
              }

              if (!options.isMergeableObject(source[key]) || !target[key]) {
                destination[key] = cloneUnlessOtherwiseSpecified(source[key], options)
              } else {
                destination[key] = getMergeFunction(key, options)(target[key], source[key], options)
              }
            })
            return destination
          }

          function deepmerge (target, source, options) {
            options = options || {}
            options.arrayMerge = options.arrayMerge || defaultArrayMerge
            options.isMergeableObject = options.isMergeableObject || isMergeableObject
            // cloneUnlessOtherwiseSpecified is added to `options` so that custom arrayMerge()
            // implementations can use it. The caller may not replace it.
            options.cloneUnlessOtherwiseSpecified = cloneUnlessOtherwiseSpecified

            let sourceIsArray = Array.isArray(source)
            let targetIsArray = Array.isArray(target)
            let sourceAndTargetTypesMatch = sourceIsArray === targetIsArray

            if (!sourceAndTargetTypesMatch) {
              return cloneUnlessOtherwiseSpecified(source, options)
            } else if (sourceIsArray) {
              return options.arrayMerge(target, source, options)
            } 
              return mergeObject(target, source, options)
            
          }

          deepmerge.all = function deepmergeAll (array, options) {
            if (!Array.isArray(array)) {
              throw new Error('first argument should be an array')
            }

            return array.reduce((prev, next) => {
              return deepmerge(prev, next, options)
            }, {})
          }

          let deepmerge_1 = deepmerge

          module.exports = deepmerge_1
          /***/
        },
        /* 1 */
        /***/ function (module, __webpack_exports__, __webpack_require__) {
          /* WEBPACK VAR INJECTION */ (function (global, module) {
            /* harmony import */ let _ponyfill_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(3)
            /* global window */

            let root

            if (typeof self !== 'undefined') {
              root = self
            } else if (typeof window !== 'undefined') {
              root = window
            } else if (typeof global !== 'undefined') {
              root = global
            } else {
              root = module
            }

            let result = Object(_ponyfill_js__WEBPACK_IMPORTED_MODULE_0__[/* default */ 'a'])(root)
            /* harmony default export */ __webpack_exports__.a = result
            /* WEBPACK VAR INJECTION */
          }.call(this, __webpack_require__(5), __webpack_require__(6)(module)))
          /***/
        },
        /* 2 */
        /***/ function (module, exports, __webpack_require__) {
          /*!
           * Fuse.js v3.4.5 - Lightweight fuzzy-search (http://fusejs.io)
           *
           * Copyright (c) 2012-2017 Kirollos Risk (http://kiro.me)
           * All Rights Reserved. Apache Software License 2.0
           *
           * http://www.apache.org/licenses/LICENSE-2.0
           */
          !(function (e, t) {
            module.exports = t()
          })(this, () => {
            return (function (e) {
              let t = {}
              function n (r) {
                if (t[r]) return t[r].exports
                let o = (t[r] = { i: r, l: !1, exports: {} })
                return e[r].call(o.exports, o, o.exports, n), (o.l = !0), o.exports
              }
              return (
                (n.m = e),
                (n.c = t),
                (n.d = function (e, t, r) {
                  n.o(e, t) || Object.defineProperty(e, t, { enumerable: !0, get: r })
                }),
                (n.r = function (e) {
                  typeof Symbol !== 'undefined' &&
                    Symbol.toStringTag &&
                    Object.defineProperty(e, Symbol.toStringTag, { value: 'Module' }),
                    Object.defineProperty(e, '__esModule', { value: !0 })
                }),
                (n.t = function (e, t) {
                  if ((1 & t && (e = n(e)), 8 & t)) return e
                  if (4 & t && typeof e === 'object' && e && e.__esModule) return e
                  let r = Object.create(null)
                  if (
                    (n.r(r),
                    Object.defineProperty(r, 'default', { enumerable: !0, value: e }),
                    2 & t && typeof e !== 'string')
                  )
                    for (let o in e)
                      n.d(
                        r,
                        o,
                        ((t) => {
                          return e[t]
                        }).bind(null, o)
                      )
                  return r
                }),
                (n.n = function (e) {
                  let t =
                    e && e.__esModule
                      ? function () {
                          return e.default
                        }
                      : function () {
                          return e
                        }
                  return n.d(t, 'a', t), t
                }),
                (n.o = function (e, t) {
                  return Object.prototype.hasOwnProperty.call(e, t)
                }),
                (n.p = ''),
                n((n.s = 1))
              )
            })([
              function (e, t) {
                e.exports = function (e) {
                  return Array.isArray ? Array.isArray(e) : Object.prototype.toString.call(e) === '[object Array]'
                }
              },
              function (e, t, n) {
                function r (e) {
                  return (r =
                    typeof Symbol === 'function' && typeof Symbol.iterator === 'symbol'
                      ? function (e) {
                          return typeof e
                        }
                      : function (e) {
                          return e && typeof Symbol === 'function' && e.constructor === Symbol && e !== Symbol.prototype
                            ? 'symbol'
                            : typeof e
                        })(e)
                }
                function o (e, t) {
                  for (let n = 0; n < t.length; n++) {
                    let r = t[n]
                    ;(r.enumerable = r.enumerable || !1),
                      (r.configurable = !0),
                      'value' in r && (r.writable = !0),
                      Object.defineProperty(e, r.key, r)
                  }
                }
                let i = n(2)
                let a = n(8)
                let s = n(0)
                let c = (function () {
                  function e (t, n) {
                    let r = n.location
                    let o = void 0 === r ? 0 : r
                    let i = n.distance
                    let s = void 0 === i ? 100 : i
                    let c = n.threshold
                    let h = void 0 === c ? 0.6 : c
                    let l = n.maxPatternLength
                    let u = void 0 === l ? 32 : l
                    let f = n.caseSensitive
                    let d = void 0 !== f && f
                    let v = n.tokenSeparator
                    let p = void 0 === v ? / +/g : v
                    let g = n.findAllMatches
                    let y = void 0 !== g && g
                    let m = n.minMatchCharLength
                    let k = void 0 === m ? 1 : m
                    let S = n.id
                    let x = void 0 === S ? null : S
                    let b = n.keys
                    let M = void 0 === b ? [] : b
                    let _ = n.shouldSort
                    let L = void 0 === _ || _
                    let w = n.getFn
                    let A = void 0 === w ? a : w
                    let C = n.sortFn
                    let I =
                      void 0 === C
                        ? function (e, t) {
                            return e.score - t.score
                          }
                        : C
                    let O = n.tokenize
                    let j = void 0 !== O && O
                    let P = n.matchAllTokens
                    let F = void 0 !== P && P
                    let T = n.includeMatches
                    let z = void 0 !== T && T
                    let E = n.includeScore
                    let K = void 0 !== E && E
                    let $ = n.verbose
                    let J = void 0 !== $ && $
                    !(function (e, t) {
                      if (!(e instanceof t)) throw new TypeError('Cannot call a class as a function')
                    })(this, e),
                      (this.options = {
                        location: o,
                        distance: s,
                        threshold: h,
                        maxPatternLength: u,
                        isCaseSensitive: d,
                        tokenSeparator: p,
                        findAllMatches: y,
                        minMatchCharLength: k,
                        id: x,
                        keys: M,
                        includeMatches: z,
                        includeScore: K,
                        shouldSort: L,
                        getFn: A,
                        sortFn: I,
                        verbose: J,
                        tokenize: j,
                        matchAllTokens: F
                      }),
                      this.setCollection(t)
                  }
                  let t, n
                  return (
                    (t = e),
                    (n = [
                      {
                        key: 'setCollection',
                        value (e) {
                          return (this.list = e), e
                        }
                      },
                      {
                        key: 'search',
                        value (e) {
                          let t = arguments.length > 1 && void 0 !== arguments[1] ? arguments[1] : { limit: !1 }
                          this._log('---------\nSearch pattern: "'.concat(e, '"'))
                          let n = this._prepareSearchers(e)
                          let r = n.tokenSearchers
                          let o = n.fullSearcher
                          let i = this._search(r, o)
                          let a = i.weights
                          let s = i.results
                          return (
                            this._computeScore(a, s),
                            this.options.shouldSort && this._sort(s),
                            t.limit && typeof t.limit === 'number' && (s = s.slice(0, t.limit)),
                            this._format(s)
                          )
                        }
                      },
                      {
                        key: '_prepareSearchers',
                        value () {
                          let e = arguments.length > 0 && void 0 !== arguments[0] ? arguments[0] : ''
                          let t = []
                          if (this.options.tokenize)
                            for (let n = e.split(this.options.tokenSeparator), r = 0, o = n.length; r < o; r += 1)
                              t.push(new i(n[r], this.options))
                          return { tokenSearchers: t, fullSearcher: new i(e, this.options) }
                        }
                      },
                      {
                        key: '_search',
                        value () {
                          let e = arguments.length > 0 && void 0 !== arguments[0] ? arguments[0] : []
                          let t = arguments.length > 1 ? arguments[1] : void 0
                          let n = this.list
                          let r = {}
                          let o = []
                          if (typeof n[0] === 'string') {
                            for (let i = 0, a = n.length; i < a; i += 1)
                              this._analyze(
                                { key: '', value: n[i], record: i, index: i },
                                { resultMap: r, results: o, tokenSearchers: e, fullSearcher: t }
                              )
                            return { weights: null, results: o }
                          }
                          for (var s = {}, c = 0, h = n.length; c < h; c += 1)
                            for (let l = n[c], u = 0, f = this.options.keys.length; u < f; u += 1) {
                              let d = this.options.keys[u]
                              if (typeof d !== 'string') {
                                if (((s[d.name] = { weight: 1 - d.weight || 1 }), d.weight <= 0 || d.weight > 1))
                                  throw new Error('Key weight has to be > 0 and <= 1')
                                d = d.name
                              } else s[d] = { weight: 1 }
                              this._analyze(
                                { key: d, value: this.options.getFn(l, d), record: l, index: c },
                                { resultMap: r, results: o, tokenSearchers: e, fullSearcher: t }
                              )
                            }
                          return { weights: s, results: o }
                        }
                      },
                      {
                        key: '_analyze',
                        value (e, t) {
                          let n = e.key
                          let r = e.arrayIndex
                          let o = void 0 === r ? -1 : r
                          let i = e.value
                          let a = e.record
                          let c = e.index
                          let h = t.tokenSearchers
                          let l = void 0 === h ? [] : h
                          let u = t.fullSearcher
                          let f = void 0 === u ? [] : u
                          let d = t.resultMap
                          let v = void 0 === d ? {} : d
                          let p = t.results
                          let g = void 0 === p ? [] : p
                          if (i != null) {
                            let y = !1
                            let m = -1
                            let k = 0
                            if (typeof i === 'string') {
                              this._log('\nKey: '.concat(n === '' ? '-' : n))
                              let S = f.search(i)
                              if (
                                (this._log('Full text: "'.concat(i, '", score: ').concat(S.score)),
                                this.options.tokenize)
                              ) {
                                for (
                                  var x = i.split(this.options.tokenSeparator), b = [], M = 0;
                                  M < l.length;
                                  M += 1
                                ) {
                                  let _ = l[M]
                                  this._log('\nPattern: "'.concat(_.pattern, '"'))
                                  for (var L = !1, w = 0; w < x.length; w += 1) {
                                    let A = x[w]
                                    let C = _.search(A)
                                    let I = {}
                                    C.isMatch
                                      ? ((I[A] = C.score), (y = !0), (L = !0), b.push(C.score))
                                      : ((I[A] = 1), this.options.matchAllTokens || b.push(1)),
                                      this._log('Token: "'.concat(A, '", score: ').concat(I[A]))
                                  }
                                  L && (k += 1)
                                }
                                m = b[0]
                                for (var O = b.length, j = 1; j < O; j += 1) m += b[j]
                                ;(m /= O), this._log('Token score average:', m)
                              }
                              let P = S.score
                              m > -1 && (P = (P + m) / 2), this._log('Score average:', P)
                              let F = !this.options.tokenize || !this.options.matchAllTokens || k >= l.length
                              if ((this._log('\nCheck Matches: '.concat(F)), (y || S.isMatch) && F)) {
                                let T = v[c]
                                T
                                  ? T.output.push({
                                      key: n,
                                      arrayIndex: o,
                                      value: i,
                                      score: P,
                                      matchedIndices: S.matchedIndices
                                    })
                                  : ((v[c] = {
                                      item: a,
                                      output: [
                                        { key: n, arrayIndex: o, value: i, score: P, matchedIndices: S.matchedIndices }
                                      ]
                                    }),
                                    g.push(v[c]))
                              }
                            } else if (s(i))
                              for (let z = 0, E = i.length; z < E; z += 1)
                                this._analyze(
                                  { key: n, arrayIndex: z, value: i[z], record: a, index: c },
                                  { resultMap: v, results: g, tokenSearchers: l, fullSearcher: f }
                                )
                          }
                        }
                      },
                      {
                        key: '_computeScore',
                        value (e, t) {
                          this._log('\n\nComputing score:\n')
                          for (let n = 0, r = t.length; n < r; n += 1) {
                            for (var o = t[n].output, i = o.length, a = 1, s = 1, c = 0; c < i; c += 1) {
                              let h = e ? e[o[c].key].weight : 1
                              let l = (h === 1 ? o[c].score : o[c].score || 0.001) * h
                              h !== 1 ? (s = Math.min(s, l)) : ((o[c].nScore = l), (a *= l))
                            }
                            (t[n].score = s === 1 ? a : s), this._log(t[n])
                          }
                        }
                      },
                      {
                        key: '_sort',
                        value (e) {
                          this._log('\n\nSorting....'), e.sort(this.options.sortFn)
                        }
                      },
                      {
                        key: '_format',
                        value (e) {
                          let t = []
                          if (this.options.verbose) {
                            let n = []
                            this._log(
                              '\n\nOutput:\n\n',
                              JSON.stringify(e, (e, t) => {
                                if (r(t) === 'object' && t !== null) {
                                  if (n.indexOf(t) !== -1) return
                                  n.push(t)
                                }
                                return t
                              })
                            ),
                              (n = null)
                          }
                          let o = []
                          this.options.includeMatches &&
                            o.push((e, t) => {
                              let n = e.output
                              t.matches = []
                              for (let r = 0, o = n.length; r < o; r += 1) {
                                let i = n[r]
                                if (i.matchedIndices.length !== 0) {
                                  let a = { indices: i.matchedIndices, value: i.value }
                                  i.key && (a.key = i.key),
                                    i.hasOwnProperty('arrayIndex') &&
                                      i.arrayIndex > -1 &&
                                      (a.arrayIndex = i.arrayIndex),
                                    t.matches.push(a)
                                }
                              }
                            }),
                            this.options.includeScore &&
                              o.push((e, t) => {
                                t.score = e.score
                              })
                          for (let i = 0, a = e.length; i < a; i += 1) {
                            let s = e[i]
                            if (
                              (this.options.id && (s.item = this.options.getFn(s.item, this.options.id)[0]), o.length)
                            ) {
                              for (var c = { item: s.item }, h = 0, l = o.length; h < l; h += 1) o[h](s, c)
                              t.push(c)
                            } else t.push(s.item)
                          }
                          return t
                        }
                      },
                      {
                        key: '_log',
                        value () {
                          let e
                          this.options.verbose && (e = console).log.apply(e, arguments)
                        }
                      }
                    ]) && o(t.prototype, n),
                    e
                  )
                })()
                e.exports = c
              },
              function (e, t, n) {
                function r (e, t) {
                  for (let n = 0; n < t.length; n++) {
                    let r = t[n]
                    ;(r.enumerable = r.enumerable || !1),
                      (r.configurable = !0),
                      'value' in r && (r.writable = !0),
                      Object.defineProperty(e, r.key, r)
                  }
                }
                let o = n(3)
                let i = n(4)
                let a = n(7)
                let s = (function () {
                  function e (t, n) {
                    let r = n.location
                    let o = void 0 === r ? 0 : r
                    let i = n.distance
                    let s = void 0 === i ? 100 : i
                    let c = n.threshold
                    let h = void 0 === c ? 0.6 : c
                    let l = n.maxPatternLength
                    let u = void 0 === l ? 32 : l
                    let f = n.isCaseSensitive
                    let d = void 0 !== f && f
                    let v = n.tokenSeparator
                    let p = void 0 === v ? / +/g : v
                    let g = n.findAllMatches
                    let y = void 0 !== g && g
                    let m = n.minMatchCharLength
                    let k = void 0 === m ? 1 : m
                    !(function (e, t) {
                      if (!(e instanceof t)) throw new TypeError('Cannot call a class as a function')
                    })(this, e),
                      (this.options = {
                        location: o,
                        distance: s,
                        threshold: h,
                        maxPatternLength: u,
                        isCaseSensitive: d,
                        tokenSeparator: p,
                        findAllMatches: y,
                        minMatchCharLength: k
                      }),
                      (this.pattern = this.options.isCaseSensitive ? t : t.toLowerCase()),
                      this.pattern.length <= u && (this.patternAlphabet = a(this.pattern))
                  }
                  let t, n
                  return (
                    (t = e),
                    (n = [
                      {
                        key: 'search',
                        value (e) {
                          if ((this.options.isCaseSensitive || (e = e.toLowerCase()), this.pattern === e))
                            return { isMatch: !0, score: 0, matchedIndices: [[0, e.length - 1]] }
                          let t = this.options
                          let n = t.maxPatternLength
                          let r = t.tokenSeparator
                          if (this.pattern.length > n) return o(e, this.pattern, r)
                          let a = this.options
                          let s = a.location
                          let c = a.distance
                          let h = a.threshold
                          let l = a.findAllMatches
                          let u = a.minMatchCharLength
                          return i(e, this.pattern, this.patternAlphabet, {
                            location: s,
                            distance: c,
                            threshold: h,
                            findAllMatches: l,
                            minMatchCharLength: u
                          })
                        }
                      }
                    ]) && r(t.prototype, n),
                    e
                  )
                })()
                e.exports = s
              },
              function (e, t) {
                let n = /[\-\[\]\/\{\}\(\)\*\+\?\.\\\^\$\|]/g
                e.exports = function (e, t) {
                  let r = arguments.length > 2 && void 0 !== arguments[2] ? arguments[2] : / +/g
                  let o = new RegExp(t.replace(n, '\\$&').replace(r, '|'))
                  let i = e.match(o)
                  let a = !!i
                  let s = []
                  if (a)
                    for (let c = 0, h = i.length; c < h; c += 1) {
                      let l = i[c]
                      s.push([e.indexOf(l), l.length - 1])
                    }
                  return { score: a ? 0.5 : 1, isMatch: a, matchedIndices: s }
                }
              },
              function (e, t, n) {
                let r = n(5)
                let o = n(6)
                e.exports = function (e, t, n, i) {
                  for (
                    var a = i.location,
                      s = void 0 === a ? 0 : a,
                      c = i.distance,
                      h = void 0 === c ? 100 : c,
                      l = i.threshold,
                      u = void 0 === l ? 0.6 : l,
                      f = i.findAllMatches,
                      d = void 0 !== f && f,
                      v = i.minMatchCharLength,
                      p = void 0 === v ? 1 : v,
                      g = s,
                      y = e.length,
                      m = u,
                      k = e.indexOf(t, g),
                      S = t.length,
                      x = [],
                      b = 0;
                    b < y;
                    b += 1
                  )
                    x[b] = 0
                  if (k !== -1) {
                    let M = r(t, { errors: 0, currentLocation: k, expectedLocation: g, distance: h })
                    if (((m = Math.min(M, m)), (k = e.lastIndexOf(t, g + S)) !== -1)) {
                      let _ = r(t, { errors: 0, currentLocation: k, expectedLocation: g, distance: h })
                      m = Math.min(_, m)
                    }
                  }
                  k = -1
                  for (var L = [], w = 1, A = S + y, C = 1 << (S - 1), I = 0; I < S; I += 1) {
                    for (var O = 0, j = A; O < j; ) {
                      r(t, { errors: I, currentLocation: g + j, expectedLocation: g, distance: h }) <= m
                        ? (O = j)
                        : (A = j),
                        (j = Math.floor((A - O) / 2 + O))
                    }
                    A = j
                    let P = Math.max(1, g - j + 1)
                    let F = d ? y : Math.min(g + j, y) + S
                    let T = Array(F + 2)
                    T[F + 1] = (1 << I) - 1
                    for (let z = F; z >= P; z -= 1) {
                      let E = z - 1
                      let K = n[e.charAt(E)]
                      if (
                        (K && (x[E] = 1),
                        (T[z] = ((T[z + 1] << 1) | 1) & K),
                        I !== 0 && (T[z] |= ((L[z + 1] | L[z]) << 1) | 1 | L[z + 1]),
                        T[z] & C &&
                          (w = r(t, { errors: I, currentLocation: E, expectedLocation: g, distance: h })) <= m)
                      ) {
                        if (((m = w), (k = E) <= g)) break
                        P = Math.max(1, 2 * g - k)
                      }
                    }
                    if (r(t, { errors: I + 1, currentLocation: g, expectedLocation: g, distance: h }) > m) break
                    L = T
                  }
                  return { isMatch: k >= 0, score: w === 0 ? 0.001 : w, matchedIndices: o(x, p) }
                }
              },
              function (e, t) {
                e.exports = function (e, t) {
                  let n = t.errors
                  let r = void 0 === n ? 0 : n
                  let o = t.currentLocation
                  let i = void 0 === o ? 0 : o
                  let a = t.expectedLocation
                  let s = void 0 === a ? 0 : a
                  let c = t.distance
                  let h = void 0 === c ? 100 : c
                  let l = r / e.length
                  let u = Math.abs(s - i)
                  return h ? l + u / h : u ? 1 : l
                }
              },
              function (e, t) {
                e.exports = function () {
                  for (
                    var e = arguments.length > 0 && void 0 !== arguments[0] ? arguments[0] : [],
                      t = arguments.length > 1 && void 0 !== arguments[1] ? arguments[1] : 1,
                      n = [],
                      r = -1,
                      o = -1,
                      i = 0,
                      a = e.length;
                    i < a;
                    i += 1
                  ) {
                    let s = e[i]
                    s && r === -1 ? (r = i) : s || r === -1 || ((o = i - 1) - r + 1 >= t && n.push([r, o]), (r = -1))
                  }
                  return e[i - 1] && i - r >= t && n.push([r, i - 1]), n
                }
              },
              function (e, t) {
                e.exports = function (e) {
                  for (var t = {}, n = e.length, r = 0; r < n; r += 1) t[e.charAt(r)] = 0
                  for (let o = 0; o < n; o += 1) t[e.charAt(o)] |= 1 << (n - o - 1)
                  return t
                }
              },
              function (e, t, n) {
                let r = n(0)
                e.exports = function (e, t) {
                  return (function e (t, n, o) {
                    if (n) {
                      let i = n.indexOf('.')
                      let a = n
                      let s = null
                      i !== -1 && ((a = n.slice(0, i)), (s = n.slice(i + 1)))
                      let c = t[a]
                      if (c != null)
                        if (s || (typeof c !== 'string' && typeof c !== 'number'))
                          if (r(c)) for (let h = 0, l = c.length; h < l; h += 1) e(c[h], s, o)
                          else s && e(c, s, o)
                        else o.push(c.toString())
                    } else o.push(t)
                    return o
                  })(e, t, [])
                }
              }
            ])
          })
          /***/
        },
        /* 3 */
        /***/ function (module, __webpack_exports__, __webpack_require__) {
          /* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, 'a', () => {
            return symbolObservablePonyfill
          })
          function symbolObservablePonyfill (root) {
            let result
            let Symbol = root.Symbol

            if (typeof Symbol === 'function') {
              if (Symbol.observable) {
                result = Symbol.observable
              } else {
                result = Symbol('observable')
                Symbol.observable = result
              }
            } else {
              result = '@@observable'
            }

            return result
          }
          /***/
        },
        /* 4 */
        /***/ function (module, exports, __webpack_require__) {
          module.exports = __webpack_require__(7)
          /***/
        },
        /* 5 */
        /***/ function (module, exports) {
          let g

          // This works in non-strict mode
          g = (function () {
            return this
          })()

          try {
            // This works if eval is allowed (see CSP)
            g = g || new Function('return this')()
          } catch (e) {
            // This works if the window reference is available
            if (typeof window === 'object') g = window
          }

          // g can still be undefined, but nothing to do about it...
          // We return undefined, instead of nothing here, so it's
          // easier to handle this case. if(!global) { ...}

          module.exports = g
          /***/
        },
        /* 6 */
        /***/ function (module, exports) {
          module.exports = function (originalModule) {
            if (!originalModule.webpackPolyfill) {
              var module = Object.create(originalModule)
              // module.parent = undefined by default
              if (!module.children) module.children = []
              Object.defineProperty(module, 'loaded', {
                enumerable: true,
                get () {
                  return module.l
                }
              })
              Object.defineProperty(module, 'id', {
                enumerable: true,
                get () {
                  return module.i
                }
              })
              Object.defineProperty(module, 'exports', {
                enumerable: true
              })
              module.webpackPolyfill = 1
            }
            return module
          }
          /***/
        },
        /* 7 */
        /***/ function (module, __webpack_exports__, __webpack_require__) {
          __webpack_require__.r(__webpack_exports__)

          // EXTERNAL MODULE: ./node_modules/fuse.js/dist/fuse.js
          let dist_fuse = __webpack_require__(2)
          let fuse_default = /* #__PURE__ */ __webpack_require__.n(dist_fuse)

          // EXTERNAL MODULE: ./node_modules/deepmerge/dist/cjs.js
          let cjs = __webpack_require__(0)
          let cjs_default = /* #__PURE__ */ __webpack_require__.n(cjs)

          // EXTERNAL MODULE: ./node_modules/symbol-observable/es/index.js
          let es = __webpack_require__(1)

          // CONCATENATED MODULE: ./node_modules/redux/es/redux.js

          /**
           * These are private action types reserved by Redux.
           * For any unknown actions, you must return the current state.
           * If the current state is undefined, you must return the initial state.
           * Do not reference these action types directly in your code.
           */
          let randomString = function randomString () {
            return Math.random()
              .toString(36)
              .substring(7)
              .split('')
              .join('.')
          }

          let ActionTypes = {
            INIT: `@@redux/INIT${  randomString()}`,
            REPLACE: `@@redux/REPLACE${  randomString()}`,
            PROBE_UNKNOWN_ACTION: function PROBE_UNKNOWN_ACTION () {
              return `@@redux/PROBE_UNKNOWN_ACTION${  randomString()}`
            }
          }

          /**
           * @param {any} obj The object to inspect.
           * @returns {boolean} True if the argument appears to be a plain object.
           */
          function isPlainObject (obj) {
            if (typeof obj !== 'object' || obj === null) return false
            let proto = obj

            while (Object.getPrototypeOf(proto) !== null) {
              proto = Object.getPrototypeOf(proto)
            }

            return Object.getPrototypeOf(obj) === proto
          }

          /**
           * Creates a Redux store that holds the state tree.
           * The only way to change the data in the store is to call `dispatch()` on it.
           *
           * There should only be a single store in your app. To specify how different
           * parts of the state tree respond to actions, you may combine several reducers
           * into a single reducer function by using `combineReducers`.
           *
           * @param {Function} reducer A function that returns the next state tree, given
           * the current state tree and the action to handle.
           *
           * @param {any} [preloadedState] The initial state. You may optionally specify it
           * to hydrate the state from the server in universal apps, or to restore a
           * previously serialized user session.
           * If you use `combineReducers` to produce the root reducer function, this must be
           * an object with the same shape as `combineReducers` keys.
           *
           * @param {Function} [enhancer] The store enhancer. You may optionally specify it
           * to enhance the store with third-party capabilities such as middleware,
           * time travel, persistence, etc. The only store enhancer that ships with Redux
           * is `applyMiddleware()`.
           *
           * @returns {Store} A Redux store that lets you read the state, dispatch actions
           * and subscribe to changes.
           */

          function createStore (reducer, preloadedState, enhancer) {
            let _ref2

            if (
              (typeof preloadedState === 'function' && typeof enhancer === 'function') ||
              (typeof enhancer === 'function' && typeof arguments[3] === 'function')
            ) {
              throw new Error(
                'It looks like you are passing several store enhancers to ' +
                  'createStore(). This is not supported. Instead, compose them ' +
                  'together to a single function.'
              )
            }

            if (typeof preloadedState === 'function' && typeof enhancer === 'undefined') {
              enhancer = preloadedState
              preloadedState = undefined
            }

            if (typeof enhancer !== 'undefined') {
              if (typeof enhancer !== 'function') {
                throw new Error('Expected the enhancer to be a function.')
              }

              return enhancer(createStore)(reducer, preloadedState)
            }

            if (typeof reducer !== 'function') {
              throw new Error('Expected the reducer to be a function.')
            }

            let currentReducer = reducer
            let currentState = preloadedState
            let currentListeners = []
            let nextListeners = currentListeners
            let isDispatching = false
            /**
             * This makes a shallow copy of currentListeners so we can use
             * nextListeners as a temporary list while dispatching.
             *
             * This prevents any bugs around consumers calling
             * subscribe/unsubscribe in the middle of a dispatch.
             */

            function ensureCanMutateNextListeners () {
              if (nextListeners === currentListeners) {
                nextListeners = currentListeners.slice()
              }
            }
            /**
             * Reads the state tree managed by the store.
             *
             * @returns {any} The current state tree of your application.
             */

            function getState () {
              if (isDispatching) {
                throw new Error(
                  'You may not call store.getState() while the reducer is executing. ' +
                    'The reducer has already received the state as an argument. ' +
                    'Pass it down from the top reducer instead of reading it from the store.'
                )
              }

              return currentState
            }
            /**
             * Adds a change listener. It will be called any time an action is dispatched,
             * and some part of the state tree may potentially have changed. You may then
             * call `getState()` to read the current state tree inside the callback.
             *
             * You may call `dispatch()` from a change listener, with the following
             * caveats:
             *
             * 1. The subscriptions are snapshotted just before every `dispatch()` call.
             * If you subscribe or unsubscribe while the listeners are being invoked, this
             * will not have any effect on the `dispatch()` that is currently in progress.
             * However, the next `dispatch()` call, whether nested or not, will use a more
             * recent snapshot of the subscription list.
             *
             * 2. The listener should not expect to see all state changes, as the state
             * might have been updated multiple times during a nested `dispatch()` before
             * the listener is called. It is, however, guaranteed that all subscribers
             * registered before the `dispatch()` started will be called with the latest
             * state by the time it exits.
             *
             * @param {Function} listener A callback to be invoked on every dispatch.
             * @returns {Function} A function to remove this change listener.
             */

            function subscribe (listener) {
              if (typeof listener !== 'function') {
                throw new Error('Expected the listener to be a function.')
              }

              if (isDispatching) {
                throw new Error(
                  'You may not call store.subscribe() while the reducer is executing. ' +
                    'If you would like to be notified after the store has been updated, subscribe from a ' +
                    'component and invoke store.getState() in the callback to access the latest state. ' +
                    'See https://redux.js.org/api-reference/store#subscribe(listener) for more details.'
                )
              }

              let isSubscribed = true
              ensureCanMutateNextListeners()
              nextListeners.push(listener)
              return function unsubscribe () {
                if (!isSubscribed) {
                  return
                }

                if (isDispatching) {
                  throw new Error(
                    'You may not unsubscribe from a store listener while the reducer is executing. ' +
                      'See https://redux.js.org/api-reference/store#subscribe(listener) for more details.'
                  )
                }

                isSubscribed = false
                ensureCanMutateNextListeners()
                let index = nextListeners.indexOf(listener)
                nextListeners.splice(index, 1)
              }
            }
            /**
             * Dispatches an action. It is the only way to trigger a state change.
             *
             * The `reducer` function, used to create the store, will be called with the
             * current state tree and the given `action`. Its return value will
             * be considered the **next** state of the tree, and the change listeners
             * will be notified.
             *
             * The base implementation only supports plain object actions. If you want to
             * dispatch a Promise, an Observable, a thunk, or something else, you need to
             * wrap your store creating function into the corresponding middleware. For
             * example, see the documentation for the `redux-thunk` package. Even the
             * middleware will eventually dispatch plain object actions using this method.
             *
             * @param {Object} action A plain object representing “what changed”. It is
             * a good idea to keep actions serializable so you can record and replay user
             * sessions, or use the time travelling `redux-devtools`. An action must have
             * a `type` property which may not be `undefined`. It is a good idea to use
             * string constants for action types.
             *
             * @returns {Object} For convenience, the same action object you dispatched.
             *
             * Note that, if you use a custom middleware, it may wrap `dispatch()` to
             * return something else (for example, a Promise you can await).
             */

            function dispatch (action) {
              if (!isPlainObject(action)) {
                throw new Error('Actions must be plain objects. ' + 'Use custom middleware for async actions.')
              }

              if (typeof action.type === 'undefined') {
                throw new Error(
                  'Actions may not have an undefined "type" property. ' + 'Have you misspelled a constant?'
                )
              }

              if (isDispatching) {
                throw new Error('Reducers may not dispatch actions.')
              }

              try {
                isDispatching = true
                currentState = currentReducer(currentState, action)
              } finally {
                isDispatching = false
              }

              let listeners = (currentListeners = nextListeners)

              for (let i = 0; i < listeners.length; i++) {
                let listener = listeners[i]
                listener()
              }

              return action
            }
            /**
             * Replaces the reducer currently used by the store to calculate the state.
             *
             * You might need this if your app implements code splitting and you want to
             * load some of the reducers dynamically. You might also need this if you
             * implement a hot reloading mechanism for Redux.
             *
             * @param {Function} nextReducer The reducer for the store to use instead.
             * @returns {void}
             */

            function replaceReducer (nextReducer) {
              if (typeof nextReducer !== 'function') {
                throw new Error('Expected the nextReducer to be a function.')
              }

              currentReducer = nextReducer // This action has a similiar effect to ActionTypes.INIT.
              // Any reducers that existed in both the new and old rootReducer
              // will receive the previous state. This effectively populates
              // the new state tree with any relevant data from the old one.

              dispatch({
                type: ActionTypes.REPLACE
              })
            }
            /**
             * Interoperability point for observable/reactive libraries.
             * @returns {observable} A minimal observable of state changes.
             * For more information, see the observable proposal:
             * https://github.com/tc39/proposal-observable
             */

            function observable () {
              let _ref

              let outerSubscribe = subscribe
              return (
                (_ref = {
                  /**
                   * The minimal observable subscription method.
                   * @param {Object} observer Any object that can be used as an observer.
                   * The observer object should have a `next` method.
                   * @returns {subscription} An object with an `unsubscribe` method that can
                   * be used to unsubscribe the observable from the store, and prevent further
                   * emission of values from the observable.
                   */
                  subscribe: function subscribe (observer) {
                    if (typeof observer !== 'object' || observer === null) {
                      throw new TypeError('Expected the observer to be an object.')
                    }

                    function observeState () {
                      if (observer.next) {
                        observer.next(getState())
                      }
                    }

                    observeState()
                    let unsubscribe = outerSubscribe(observeState)
                    return {
                      unsubscribe
                    }
                  }
                }),
                (_ref[es['a' /* default */]] = function () {
                  return this
                }),
                _ref
              )
            } // When a store is created, an "INIT" action is dispatched so that every
            // reducer returns their initial state. This effectively populates
            // the initial state tree.

            dispatch({
              type: ActionTypes.INIT
            })
            return (
              (_ref2 = {
                dispatch,
                subscribe,
                getState,
                replaceReducer
              }),
              (_ref2[es['a' /* default */]] = observable),
              _ref2
            )
          }

          function getUndefinedStateErrorMessage (key, action) {
            let actionType = action && action.type
            let actionDescription = (actionType && `action "${  String(actionType)  }"`) || 'an action'
            return (
              `Given ${ 
              actionDescription 
              }, reducer "${ 
              key 
              }" returned undefined. ` +
              `To ignore an action, you must explicitly return the previous state. ` +
              `If you want this reducer to hold no value, you can return null instead of undefined.`
            )
          }

          function assertReducerShape (reducers) {
            Object.keys(reducers).forEach((key) => {
              let reducer = reducers[key]
              let initialState = reducer(undefined, {
                type: ActionTypes.INIT
              })

              if (typeof initialState === 'undefined') {
                throw new Error(
                  `Reducer "${ 
                    key 
                    }" returned undefined during initialization. ` +
                    `If the state passed to the reducer is undefined, you must ` +
                    `explicitly return the initial state. The initial state may ` +
                    `not be undefined. If you don't want to set a value for this reducer, ` +
                    `you can use null instead of undefined.`
                )
              }

              if (
                typeof reducer(undefined, {
                  type: ActionTypes.PROBE_UNKNOWN_ACTION()
                }) === 'undefined'
              ) {
                throw new Error(
                  `Reducer "${ 
                    key 
                    }" returned undefined when probed with a random type. ` +
                    `Don't try to handle ${  ActionTypes.INIT  } or other actions in "redux/*" ` +
                    `namespace. They are considered private. Instead, you must return the ` +
                    `current state for any unknown actions, unless it is undefined, ` +
                    `in which case you must return the initial state, regardless of the ` +
                    `action type. The initial state may not be undefined, but can be null.`
                )
              }
            })
          }
          /**
           * Turns an object whose values are different reducer functions, into a single
           * reducer function. It will call every child reducer, and gather their results
           * into a single state object, whose keys correspond to the keys of the passed
           * reducer functions.
           *
           * @param {Object} reducers An object whose values correspond to different
           * reducer functions that need to be combined into one. One handy way to obtain
           * it is to use ES6 `import * as reducers` syntax. The reducers may never return
           * undefined for any action. Instead, they should return their initial state
           * if the state passed to them was undefined, and the current state for any
           * unrecognized action.
           *
           * @returns {Function} A reducer function that invokes every reducer inside the
           * passed object, and builds a state object with the same shape.
           */

          function combineReducers (reducers) {
            let reducerKeys = Object.keys(reducers)
            let finalReducers = {}

            for (let i = 0; i < reducerKeys.length; i++) {
              let key = reducerKeys[i]

              if (typeof reducers[key] === 'function') {
                finalReducers[key] = reducers[key]
              }
            }

            let finalReducerKeys = Object.keys(finalReducers) // This is used to make sure we don't warn about the same

            let shapeAssertionError

            try {
              assertReducerShape(finalReducers)
            } catch (e) {
              shapeAssertionError = e
            }

            return function combination (state, action) {
              if (state === void 0) {
                state = {}
              }

              if (shapeAssertionError) {
                throw shapeAssertionError
              }

              let hasChanged = false
              let nextState = {}

              for (let _i = 0; _i < finalReducerKeys.length; _i++) {
                let _key = finalReducerKeys[_i]
                let reducer = finalReducers[_key]
                let previousStateForKey = state[_key]
                let nextStateForKey = reducer(previousStateForKey, action)

                if (typeof nextStateForKey === 'undefined') {
                  let errorMessage = getUndefinedStateErrorMessage(_key, action)
                  throw new Error(errorMessage)
                }

                nextState[_key] = nextStateForKey
                hasChanged = hasChanged || nextStateForKey !== previousStateForKey
              }

              return hasChanged ? nextState : state
            }
          }

          // CONCATENATED MODULE: ./src/scripts/reducers/items.js
          let defaultState = []
          function items_items (state, action) {
            if (state === void 0) {
              state = defaultState
            }

            switch (action.type) {
              case 'ADD_ITEM': {
                // Add object to items array
                let newState = [].concat(state, [
                  {
                    id: action.id,
                    choiceId: action.choiceId,
                    groupId: action.groupId,
                    value: action.value,
                    label: action.label,
                    active: true,
                    highlighted: false,
                    customProperties: action.customProperties,
                    placeholder: action.placeholder || false,
                    keyCode: null
                  }
                ])
                return newState.map((obj) => {
                  let item = obj
                  item.highlighted = false
                  return item
                })
              }

              case 'REMOVE_ITEM': {
                // Set item to inactive
                return state.map((obj) => {
                  let item = obj

                  if (item.id === action.id) {
                    item.active = false
                  }

                  return item
                })
              }

              case 'HIGHLIGHT_ITEM': {
                return state.map((obj) => {
                  let item = obj

                  if (item.id === action.id) {
                    item.highlighted = action.highlighted
                  }

                  return item
                })
              }

              default: {
                return state
              }
            }
          }
          // CONCATENATED MODULE: ./src/scripts/reducers/groups.js
          let groups_defaultState = []
          function groups (state, action) {
            if (state === void 0) {
              state = groups_defaultState
            }

            switch (action.type) {
              case 'ADD_GROUP': {
                return [].concat(state, [
                  {
                    id: action.id,
                    value: action.value,
                    active: action.active,
                    disabled: action.disabled
                  }
                ])
              }

              case 'CLEAR_CHOICES': {
                return []
              }

              default: {
                return state
              }
            }
          }
          // CONCATENATED MODULE: ./src/scripts/reducers/choices.js
          let choices_defaultState = []
          function choices_choices (state, action) {
            if (state === void 0) {
              state = choices_defaultState
            }

            switch (action.type) {
              case 'ADD_CHOICE': {
                /*
            A disabled choice appears in the choice dropdown but cannot be selected
            A selected choice has been added to the passed input's value (added as an item)
            An active choice appears within the choice dropdown
         */
                return [].concat(state, [
                  {
                    id: action.id,
                    elementId: action.elementId,
                    groupId: action.groupId,
                    value: action.value,
                    label: action.label || action.value,
                    disabled: action.disabled || false,
                    selected: false,
                    active: true,
                    score: 9999,
                    customProperties: action.customProperties,
                    placeholder: action.placeholder || false,
                    keyCode: null
                  }
                ])
              }

              case 'ADD_ITEM': {
                // If all choices need to be activated
                if (action.activateOptions) {
                  return state.map((obj) => {
                    let choice = obj
                    choice.active = action.active
                    return choice
                  })
                } // When an item is added and it has an associated choice,
                // we want to disable it so it can't be chosen again

                if (action.choiceId > -1) {
                  return state.map((obj) => {
                    let choice = obj

                    if (choice.id === parseInt(action.choiceId, 10)) {
                      choice.selected = true
                    }

                    return choice
                  })
                }

                return state
              }

              case 'REMOVE_ITEM': {
                // When an item is removed and it has an associated choice,
                // we want to re-enable it so it can be chosen again
                if (action.choiceId > -1) {
                  return state.map((obj) => {
                    let choice = obj

                    if (choice.id === parseInt(action.choiceId, 10)) {
                      choice.selected = false
                    }

                    return choice
                  })
                }

                return state
              }

              case 'FILTER_CHOICES': {
                return state.map((obj) => {
                  let choice = obj // Set active state based on whether choice is
                  // within filtered results

                  choice.active = action.results.some((_ref) => {
                    let item = _ref.item
                    let score = _ref.score

                    if (item.id === choice.id) {
                      choice.score = score
                      return true
                    }

                    return false
                  })
                  return choice
                })
              }

              case 'ACTIVATE_CHOICES': {
                return state.map((obj) => {
                  let choice = obj
                  choice.active = action.active
                  return choice
                })
              }

              case 'CLEAR_CHOICES': {
                return choices_defaultState
              }

              default: {
                return state
              }
            }
          }
          // CONCATENATED MODULE: ./src/scripts/reducers/general.js
          let general_defaultState = {
            loading: false
          }

          let general = function general (state, action) {
            if (state === void 0) {
              state = general_defaultState
            }

            switch (action.type) {
              case 'SET_IS_LOADING': {
                return {
                  loading: action.isLoading
                }
              }

              default: {
                return state
              }
            }
          }

          /* harmony default export */ let reducers_general = general
          // CONCATENATED MODULE: ./src/scripts/lib/utils.js
          /**
           * @param {number} min
           * @param {number} max
           * @returns {number}
           */
          let getRandomNumber = function getRandomNumber (min, max) {
            return Math.floor(Math.random() * (max - min) + min)
          }
          /**
           * @param {number} length
           * @returns {string}
           */

          let generateChars = function generateChars (length) {
            return Array.from(
              {
                length
              },
              () => {
                return getRandomNumber(0, 36).toString(36)
              }
            ).join('')
          }
          /**
           * @param {HTMLInputElement | HTMLSelectElement} element
           * @param {string} prefix
           * @returns {string}
           */

          let generateId = function generateId (element, prefix) {
            let id = element.id || (element.name && `${element.name  }-${  generateChars(2)}`) || generateChars(4)
            id = id.replace(/(:|\.|\[|\]|,)/g, '')
            id = `${prefix  }-${  id}`
            return id
          }
          /**
           * @param {any} obj
           * @returns {string}
           */

          let getType = function getType (obj) {
            return Object.prototype.toString.call(obj).slice(8, -1)
          }
          /**
           * @param {string} type
           * @param {any} obj
           * @returns {boolean}
           */

          let isType = function isType (type, obj) {
            return obj !== undefined && obj !== null && getType(obj) === type
          }
          /**
           * @param {HTMLElement} element
           * @param {HTMLElement} [wrapper={HTMLDivElement}]
           * @returns {HTMLElement}
           */

          let utils_wrap = function wrap (element, wrapper) {
            if (wrapper === void 0) {
              wrapper = document.createElement('div')
            }

            if (element.nextSibling) {
              element.parentNode.insertBefore(wrapper, element.nextSibling)
            } else {
              element.parentNode.appendChild(wrapper)
            }

            return wrapper.appendChild(element)
          }
          /**
           * @param {Element} startEl
           * @param {string} selector
           * @param {1 | -1} direction
           * @returns {Element | undefined}
           */

          let getAdjacentEl = function getAdjacentEl (startEl, selector, direction) {
            if (direction === void 0) {
              direction = 1
            }

            if (!(startEl instanceof Element) || typeof selector !== 'string') {
              return undefined
            }

            let prop = `${direction > 0 ? 'next' : 'previous'  }ElementSibling`
            let sibling = startEl[prop]

            while (sibling) {
              if (sibling.matches(selector)) {
                return sibling
              }

              sibling = sibling[prop]
            }

            return sibling
          }
          /**
           * @param {Element} element
           * @param {Element} parent
           * @param {-1 | 1} direction
           * @returns {boolean}
           */

          let isScrolledIntoView = function isScrolledIntoView (element, parent, direction) {
            if (direction === void 0) {
              direction = 1
            }

            if (!element) {
              return false
            }

            let isVisible

            if (direction > 0) {
              // In view from bottom
              isVisible = parent.scrollTop + parent.offsetHeight >= element.offsetTop + element.offsetHeight
            } else {
              // In view from top
              isVisible = element.offsetTop >= parent.scrollTop
            }

            return isVisible
          }
          /**
           * @param {any} value
           * @returns {any}
           */

          let sanitise = function sanitise (value) {
            if (typeof value !== 'string') {
              return value
            }

            return value
              .replace(/&/g, '&amp;')
              .replace(/>/g, '&rt;')
              .replace(/</g, '&lt;')
              .replace(/"/g, '&quot;')
          }
          /**
           * @returns {() => (str: string) => Element}
           */

          let strToEl = (function () {
            let tmpEl = document.createElement('div')
            return function (str) {
              let cleanedInput = str.trim()
              tmpEl.innerHTML = cleanedInput
              let firldChild = tmpEl.children[0]

              while (tmpEl.firstChild) {
                tmpEl.removeChild(tmpEl.firstChild)
              }

              return firldChild
            }
          })()
          /**
           * @param {{ label?: string, value: string }} a
           * @param {{ label?: string, value: string }} b
           * @returns {number}
           */

          let sortByAlpha = function sortByAlpha (_ref, _ref2) {
            let value = _ref.value
            let _ref$label = _ref.label
            let label = _ref$label === void 0 ? value : _ref$label
            let value2 = _ref2.value
            let _ref2$label = _ref2.label
            let label2 = _ref2$label === void 0 ? value2 : _ref2$label
            return label.localeCompare(label2, [], {
              sensitivity: 'base',
              ignorePunctuation: true,
              numeric: true
            })
          }
          /**
           * @param {{ score: number }} a
           * @param {{ score: number }} b
           */

          let sortByScore = function sortByScore (a, b) {
            return a.score - b.score
          }
          /**
           * @param {HTMLElement} element
           * @param {string} type
           * @param {object} customArgs
           */

          let dispatchEvent = function dispatchEvent (element, type, customArgs) {
            if (customArgs === void 0) {
              customArgs = null
            }

            let event = new CustomEvent(type, {
              detail: customArgs,
              bubbles: true,
              cancelable: true
            })
            return element.dispatchEvent(event)
          }
          /**
           * @param {array} array
           * @param {any} value
           * @param {string} [key="value"]
           * @returns {boolean}
           */

          let existsInArray = function existsInArray (array, value, key) {
            if (key === void 0) {
              key = 'value'
            }

            return array.some((item) => {
              if (typeof value === 'string') {
                return item[key] === value.trim()
              }

              return item[key] === value
            })
          }
          /**
           * @param {any} obj
           * @returns {any}
           */

          let cloneObject = function cloneObject (obj) {
            return JSON.parse(JSON.stringify(obj))
          }
          /**
           * Returns an array of keys present on the first but missing on the second object
           * @param {object} a
           * @param {object} b
           * @returns {string[]}
           */

          let diff = function diff (a, b) {
            let aKeys = Object.keys(a).sort()
            let bKeys = Object.keys(b).sort()
            return aKeys.filter((i) => {
              return bKeys.indexOf(i) < 0
            })
          }
          // CONCATENATED MODULE: ./src/scripts/reducers/index.js

          let appReducer = combineReducers({
            items: items_items,
            groups,
            choices: choices_choices,
            general: reducers_general
          })

          let reducers_rootReducer = function rootReducer (passedState, action) {
            let state = passedState // If we are clearing all items, groups and options we reassign
            // state and then pass that state to our proper reducer. This isn't
            // mutating our actual state
            // See: http://stackoverflow.com/a/35641992

            if (action.type === 'CLEAR_ALL') {
              state = undefined
            } else if (action.type === 'RESET_TO') {
              return cloneObject(action.state)
            }

            return appReducer(state, action)
          }

          /* harmony default export */ let reducers = reducers_rootReducer
          // CONCATENATED MODULE: ./src/scripts/store/store.js
          function _defineProperties (target, props) {
            for (let i = 0; i < props.length; i++) {
              let descriptor = props[i]
              descriptor.enumerable = descriptor.enumerable || false
              descriptor.configurable = true
              if ('value' in descriptor) descriptor.writable = true
              Object.defineProperty(target, descriptor.key, descriptor)
            }
          }

          function _createClass (Constructor, protoProps, staticProps) {
            if (protoProps) _defineProperties(Constructor.prototype, protoProps)
            if (staticProps) _defineProperties(Constructor, staticProps)
            return Constructor
          }

          /**
           * @typedef {import('../../../types/index').Choices.Choice} Choice
           * @typedef {import('../../../types/index').Choices.Group} Group
           * @typedef {import('../../../types/index').Choices.Item} Item
           */

          let store_Store =
            /* #__PURE__ */
            (function () {
              function Store () {
                this._store = createStore(
                  reducers,
                  window.__REDUX_DEVTOOLS_EXTENSION__ && window.__REDUX_DEVTOOLS_EXTENSION__()
                )
              }
              /**
               * Subscribe store to function call (wrapped Redux method)
               * @param  {Function} onChange Function to trigger when state changes
               * @return
               */

              let _proto = Store.prototype

              _proto.subscribe = function subscribe (onChange) {
                this._store.subscribe(onChange)
              }
              /**
               * Dispatch event to store (wrapped Redux method)
               * @param  {{ type: string, [x: string]: any }} action Action to trigger
               * @return
               */

              _proto.dispatch = function dispatch (action) {
                this._store.dispatch(action)
              }
              /**
               * Get store object (wrapping Redux method)
               * @returns {object} State
               */

              /**
               * Get loading state from store
               * @returns {boolean} Loading State
               */
              _proto.isLoading = function isLoading () {
                return this.state.general.loading
              }
              /**
               * Get single choice by it's ID
               * @param {string} id
               * @returns {Choice | undefined} Found choice
               */

              _proto.getChoiceById = function getChoiceById (id) {
                return this.activeChoices.find((choice) => {
                  return choice.id === parseInt(id, 10)
                })
              }
              /**
               * Get group by group id
               * @param  {number} id Group ID
               * @returns {Group | undefined} Group data
               */

              _proto.getGroupById = function getGroupById (id) {
                return this.groups.find((group) => {
                  return group.id === id
                })
              }

              _createClass(Store, [
                {
                  key: 'state',
                  get: function get () {
                    return this._store.getState()
                  }
                  /**
                   * Get items from store
                   * @returns {Item[]} Item objects
                   */
                },
                {
                  key: 'items',
                  get: function get () {
                    return this.state.items
                  }
                  /**
                   * Get active items from store
                   * @returns {Item[]} Item objects
                   */
                },
                {
                  key: 'activeItems',
                  get: function get () {
                    return this.items.filter((item) => {
                      return item.active === true
                    })
                  }
                  /**
                   * Get highlighted items from store
                   * @returns {Item[]} Item objects
                   */
                },
                {
                  key: 'highlightedActiveItems',
                  get: function get () {
                    return this.items.filter((item) => {
                      return item.active && item.highlighted
                    })
                  }
                  /**
                   * Get choices from store
                   * @returns {Choice[]} Option objects
                   */
                },
                {
                  key: 'choices',
                  get: function get () {
                    return this.state.choices
                  }
                  /**
                   * Get active choices from store
                   * @returns {Choice[]} Option objects
                   */
                },
                {
                  key: 'activeChoices',
                  get: function get () {
                    return this.choices.filter((choice) => {
                      return choice.active === true
                    })
                  }
                  /**
                   * Get selectable choices from store
                   * @returns {Choice[]} Option objects
                   */
                },
                {
                  key: 'selectableChoices',
                  get: function get () {
                    return this.choices.filter((choice) => {
                      return choice.disabled !== true
                    })
                  }
                  /**
                   * Get choices that can be searched (excluding placeholders)
                   * @returns {Choice[]} Option objects
                   */
                },
                {
                  key: 'searchableChoices',
                  get: function get () {
                    return this.selectableChoices.filter((choice) => {
                      return choice.placeholder !== true
                    })
                  }
                  /**
                   * Get placeholder choice from store
                   * @returns {Choice | undefined} Found placeholder
                   */
                },
                {
                  key: 'placeholderChoice',
                  get: function get () {
                    return []
                      .concat(this.choices)
                      .reverse()
                      .find((choice) => {
                        return choice.placeholder === true
                      })
                  }
                  /**
                   * Get groups from store
                   * @returns {Group[]} Group objects
                   */
                },
                {
                  key: 'groups',
                  get: function get () {
                    return this.state.groups
                  }
                  /**
                   * Get active groups from store
                   * @returns {Group[]} Group objects
                   */
                },
                {
                  key: 'activeGroups',
                  get: function get () {
                    let groups = this.groups
                    let choices = this.choices
                    return groups.filter((group) => {
                      let isActive = group.active === true && group.disabled === false
                      let hasActiveOptions = choices.some((choice) => {
                        return choice.active === true && choice.disabled === false
                      })
                      return isActive && hasActiveOptions
                    }, [])
                  }
                }
              ])

              return Store
            })()

          // CONCATENATED MODULE: ./src/scripts/components/dropdown.js
          function dropdown_defineProperties (target, props) {
            for (let i = 0; i < props.length; i++) {
              let descriptor = props[i]
              descriptor.enumerable = descriptor.enumerable || false
              descriptor.configurable = true
              if ('value' in descriptor) descriptor.writable = true
              Object.defineProperty(target, descriptor.key, descriptor)
            }
          }

          function dropdown_createClass (Constructor, protoProps, staticProps) {
            if (protoProps) dropdown_defineProperties(Constructor.prototype, protoProps)
            if (staticProps) dropdown_defineProperties(Constructor, staticProps)
            return Constructor
          }

          /**
           * @typedef {import('../../../types/index').Choices.passedElement} passedElement
           * @typedef {import('../../../types/index').Choices.ClassNames} ClassNames
           */
          let Dropdown =
            /* #__PURE__ */
            (function () {
              /**
               * @param {{
               *  element: HTMLElement,
               *  type: passedElement['type'],
               *  classNames: ClassNames,
               * }} args
               */
              function Dropdown (_ref) {
                let element = _ref.element
                let type = _ref.type
                let classNames = _ref.classNames
                this.element = element
                this.classNames = classNames
                this.type = type
                this.isActive = false
              }
              /**
               * Bottom position of dropdown in viewport coordinates
               * @returns {number} Vertical position
               */

              let _proto = Dropdown.prototype

              /**
               * Find element that matches passed selector
               * @param {string} selector
               * @returns {HTMLElement | null}
               */
              _proto.getChild = function getChild (selector) {
                return this.element.querySelector(selector)
              }
              /**
               * Show dropdown to user by adding active state class
               * @returns {this}
               */

              _proto.show = function show () {
                this.element.classList.add(this.classNames.activeState)
                this.element.setAttribute('aria-expanded', 'true')
                this.isActive = true
                return this
              }
              /**
               * Hide dropdown from user
               * @returns {this}
               */

              _proto.hide = function hide () {
                this.element.classList.remove(this.classNames.activeState)
                this.element.setAttribute('aria-expanded', 'false')
                this.isActive = false
                return this
              }

              dropdown_createClass(Dropdown, [
                {
                  key: 'distanceFromTopWindow',
                  get: function get () {
                    return this.element.getBoundingClientRect().bottom
                  }
                }
              ])

              return Dropdown
            })()

          // CONCATENATED MODULE: ./src/scripts/constants.js

          /**
           * @typedef {import('../../types/index').Choices.ClassNames} ClassNames
           * @typedef {import('../../types/index').Choices.Options} Options
           */

          /** @type {ClassNames} */

          let DEFAULT_CLASSNAMES = {
            containerOuter: 'choices',
            containerInner: 'choices__inner',
            input: 'choices__input',
            inputCloned: 'choices__input--cloned',
            list: 'choices__list',
            listItems: 'choices__list--multiple',
            listSingle: 'choices__list--single',
            listDropdown: 'choices__list--dropdown',
            item: 'choices__item',
            itemSelectable: 'choices__item--selectable',
            itemDisabled: 'choices__item--disabled',
            itemChoice: 'choices__item--choice',
            placeholder: 'choices__placeholder',
            group: 'choices__group',
            groupHeading: 'choices__heading',
            button: 'choices__button',
            activeState: 'is-active',
            focusState: 'is-focused',
            openState: 'is-open',
            disabledState: 'is-disabled',
            highlightedState: 'is-highlighted',
            selectedState: 'is-selected',
            flippedState: 'is-flipped',
            loadingState: 'is-loading',
            noResults: 'has-no-results',
            noChoices: 'has-no-choices'
          }
          /** @type {Options} */

          let DEFAULT_CONFIG = {
            items: [],
            choices: [],
            silent: false,
            renderChoiceLimit: -1,
            maxItemCount: -1,
            addItems: true,
            addItemFilter: null,
            removeItems: true,
            removeItemButton: false,
            editItems: false,
            duplicateItemsAllowed: true,
            delimiter: ',',
            paste: true,
            searchEnabled: true,
            searchChoices: true,
            searchFloor: 1,
            searchResultLimit: 4,
            searchFields: ['label', 'value'],
            position: 'auto',
            resetScrollPosition: true,
            shouldSort: true,
            shouldSortItems: false,
            sorter: sortByAlpha,
            placeholder: true,
            placeholderValue: null,
            searchPlaceholderValue: null,
            prependValue: null,
            appendValue: null,
            renderSelectedChoices: 'auto',
            loadingText: 'Loading...',
            noResultsText: 'No results found',
            noChoicesText: 'No choices to choose from',
            itemSelectText: 'Press to select',
            uniqueItemText: 'Only unique values can be added',
            customAddItemText: 'Only values matching specific conditions can be added',
            addItemText: function addItemText (value) {
              return `Press Enter to add <b>"${  sanitise(value)  }"</b>`
            },
            maxItemText: function maxItemText (maxItemCount) {
              return `Only ${  maxItemCount  } values can be added`
            },
            valueComparer: function valueComparer (value1, value2) {
              return value1 === value2
            },
            fuseOptions: {
              includeScore: true
            },
            callbackOnInit: null,
            callbackOnCreateTemplates: null,
            classNames: DEFAULT_CLASSNAMES
          }
          let EVENTS = {
            showDropdown: 'showDropdown',
            hideDropdown: 'hideDropdown',
            change: 'change',
            choice: 'choice',
            search: 'search',
            addItem: 'addItem',
            removeItem: 'removeItem',
            highlightItem: 'highlightItem',
            highlightChoice: 'highlightChoice'
          }
          let ACTION_TYPES = {
            ADD_CHOICE: 'ADD_CHOICE',
            FILTER_CHOICES: 'FILTER_CHOICES',
            ACTIVATE_CHOICES: 'ACTIVATE_CHOICES',
            CLEAR_CHOICES: 'CLEAR_CHOICES',
            ADD_GROUP: 'ADD_GROUP',
            ADD_ITEM: 'ADD_ITEM',
            REMOVE_ITEM: 'REMOVE_ITEM',
            HIGHLIGHT_ITEM: 'HIGHLIGHT_ITEM',
            CLEAR_ALL: 'CLEAR_ALL'
          }
          let KEY_CODES = {
            BACK_KEY: 46,
            DELETE_KEY: 8,
            ENTER_KEY: 13,
            A_KEY: 65,
            ESC_KEY: 27,
            UP_KEY: 38,
            DOWN_KEY: 40,
            PAGE_UP_KEY: 33,
            PAGE_DOWN_KEY: 34
          }
          let TEXT_TYPE = 'text'
          let SELECT_ONE_TYPE = 'select-one'
          let SELECT_MULTIPLE_TYPE = 'select-multiple'
          let SCROLLING_SPEED = 4
          // CONCATENATED MODULE: ./src/scripts/components/container.js

          /**
           * @typedef {import('../../../types/index').Choices.passedElement} passedElement
           * @typedef {import('../../../types/index').Choices.ClassNames} ClassNames
           */

          let container_Container =
            /* #__PURE__ */
            (function () {
              /**
               * @param {{
               *  element: HTMLElement,
               *  type: passedElement['type'],
               *  classNames: ClassNames,
               *  position
               * }} args
               */
              function Container (_ref) {
                let element = _ref.element
                let type = _ref.type
                let classNames = _ref.classNames
                let position = _ref.position
                this.element = element
                this.classNames = classNames
                this.type = type
                this.position = position
                this.isOpen = false
                this.isFlipped = false
                this.isFocussed = false
                this.isDisabled = false
                this.isLoading = false
                this._onFocus = this._onFocus.bind(this)
                this._onBlur = this._onBlur.bind(this)
              }

              let _proto = Container.prototype

              _proto.addEventListeners = function addEventListeners () {
                this.element.addEventListener('focus', this._onFocus)
                this.element.addEventListener('blur', this._onBlur)
              }

              _proto.removeEventListeners = function removeEventListeners () {
                this.element.removeEventListener('focus', this._onFocus)
                this.element.removeEventListener('blur', this._onBlur)
              }
              /**
               * Determine whether container should be flipped based on passed
               * dropdown position
               * @param {number} dropdownPos
               * @returns {boolean}
               */

              _proto.shouldFlip = function shouldFlip (dropdownPos) {
                if (typeof dropdownPos !== 'number') {
                  return false
                } // If flip is enabled and the dropdown bottom position is
                // greater than the window height flip the dropdown.

                let shouldFlip = false

                if (this.position === 'auto') {
                  shouldFlip = !window.matchMedia(`(min-height: ${  dropdownPos + 1  }px)`).matches
                } else if (this.position === 'top') {
                  shouldFlip = true
                }

                return shouldFlip
              }
              /**
               * @param {string} activeDescendantID
               */

              _proto.setActiveDescendant = function setActiveDescendant (activeDescendantID) {
                this.element.setAttribute('aria-activedescendant', activeDescendantID)
              }

              _proto.removeActiveDescendant = function removeActiveDescendant () {
                this.element.removeAttribute('aria-activedescendant')
              }
              /**
               * @param {number} dropdownPos
               */

              _proto.open = function open (dropdownPos) {
                this.element.classList.add(this.classNames.openState)
                this.element.setAttribute('aria-expanded', 'true')
                this.isOpen = true

                if (this.shouldFlip(dropdownPos)) {
                  this.element.classList.add(this.classNames.flippedState)
                  this.isFlipped = true
                }
              }

              _proto.close = function close () {
                this.element.classList.remove(this.classNames.openState)
                this.element.setAttribute('aria-expanded', 'false')
                this.removeActiveDescendant()
                this.isOpen = false // A dropdown flips if it does not have space within the page

                if (this.isFlipped) {
                  this.element.classList.remove(this.classNames.flippedState)
                  this.isFlipped = false
                }
              }

              _proto.focus = function focus () {
                if (!this.isFocussed) {
                  this.element.focus()
                }
              }

              _proto.addFocusState = function addFocusState () {
                this.element.classList.add(this.classNames.focusState)
              }

              _proto.removeFocusState = function removeFocusState () {
                this.element.classList.remove(this.classNames.focusState)
              }

              _proto.enable = function enable () {
                this.element.classList.remove(this.classNames.disabledState)
                this.element.removeAttribute('aria-disabled')

                if (this.type === SELECT_ONE_TYPE) {
                  this.element.setAttribute('tabindex', '0')
                }

                this.isDisabled = false
              }

              _proto.disable = function disable () {
                this.element.classList.add(this.classNames.disabledState)
                this.element.setAttribute('aria-disabled', 'true')

                if (this.type === SELECT_ONE_TYPE) {
                  this.element.setAttribute('tabindex', '-1')
                }

                this.isDisabled = true
              }
              /**
               * @param {HTMLElement} element
               */

              _proto.wrap = function wrap (element) {
                utils_wrap(element, this.element)
              }
              /**
               * @param {Element} element
               */

              _proto.unwrap = function unwrap (element) {
                // Move passed element outside this element
                this.element.parentNode.insertBefore(element, this.element) // Remove this element

                this.element.parentNode.removeChild(this.element)
              }

              _proto.addLoadingState = function addLoadingState () {
                this.element.classList.add(this.classNames.loadingState)
                this.element.setAttribute('aria-busy', 'true')
                this.isLoading = true
              }

              _proto.removeLoadingState = function removeLoadingState () {
                this.element.classList.remove(this.classNames.loadingState)
                this.element.removeAttribute('aria-busy')
                this.isLoading = false
              }

              _proto._onFocus = function _onFocus () {
                this.isFocussed = true
              }

              _proto._onBlur = function _onBlur () {
                this.isFocussed = false
              }

              return Container
            })()

          // CONCATENATED MODULE: ./src/scripts/components/input.js
          function input_defineProperties (target, props) {
            for (let i = 0; i < props.length; i++) {
              let descriptor = props[i]
              descriptor.enumerable = descriptor.enumerable || false
              descriptor.configurable = true
              if ('value' in descriptor) descriptor.writable = true
              Object.defineProperty(target, descriptor.key, descriptor)
            }
          }

          function input_createClass (Constructor, protoProps, staticProps) {
            if (protoProps) input_defineProperties(Constructor.prototype, protoProps)
            if (staticProps) input_defineProperties(Constructor, staticProps)
            return Constructor
          }

          /**
           * @typedef {import('../../../types/index').Choices.passedElement} passedElement
           * @typedef {import('../../../types/index').Choices.ClassNames} ClassNames
           */

          let input_Input =
            /* #__PURE__ */
            (function () {
              /**
               * @param {{
               *  element: HTMLInputElement,
               *  type: passedElement['type'],
               *  classNames: ClassNames,
               *  preventPaste: boolean
               * }} args
               */
              function Input (_ref) {
                let element = _ref.element
                let type = _ref.type
                let classNames = _ref.classNames
                let preventPaste = _ref.preventPaste
                this.element = element
                this.type = type
                this.classNames = classNames
                this.preventPaste = preventPaste
                this.isFocussed = this.element === document.activeElement
                this.isDisabled = element.disabled
                this._onPaste = this._onPaste.bind(this)
                this._onInput = this._onInput.bind(this)
                this._onFocus = this._onFocus.bind(this)
                this._onBlur = this._onBlur.bind(this)
              }
              /**
               * @param {string} placeholder
               */

              let _proto = Input.prototype

              _proto.addEventListeners = function addEventListeners () {
                this.element.addEventListener('paste', this._onPaste)
                this.element.addEventListener('input', this._onInput, {
                  passive: true
                })
                this.element.addEventListener('focus', this._onFocus, {
                  passive: true
                })
                this.element.addEventListener('blur', this._onBlur, {
                  passive: true
                })
              }

              _proto.removeEventListeners = function removeEventListeners () {
                this.element.removeEventListener('input', this._onInput, {
                  passive: true
                })
                this.element.removeEventListener('paste', this._onPaste)
                this.element.removeEventListener('focus', this._onFocus, {
                  passive: true
                })
                this.element.removeEventListener('blur', this._onBlur, {
                  passive: true
                })
              }

              _proto.enable = function enable () {
                this.element.removeAttribute('disabled')
                this.isDisabled = false
              }

              _proto.disable = function disable () {
                this.element.setAttribute('disabled', '')
                this.isDisabled = true
              }

              _proto.focus = function focus () {
                if (!this.isFocussed) {
                  this.element.focus()
                }
              }

              _proto.blur = function blur () {
                if (this.isFocussed) {
                  this.element.blur()
                }
              }
              /**
               * Set value of input to blank
               * @param {boolean} setWidth
               * @returns {this}
               */

              _proto.clear = function clear (setWidth) {
                if (setWidth === void 0) {
                  setWidth = true
                }

                if (this.element.value) {
                  this.element.value = ''
                }

                if (setWidth) {
                  this.setWidth()
                }

                return this
              }
              /**
               * Set the correct input width based on placeholder
               * value or input value
               */

              _proto.setWidth = function setWidth () {
                // Resize input to contents or placeholder
                let _this$element = this.element
                let style = _this$element.style
                let value = _this$element.value
                let placeholder = _this$element.placeholder
                style.minWidth = `${placeholder.length + 1  }ch`
                style.width = `${value.length + 1  }ch`
              }
              /**
               * @param {string} activeDescendantID
               */

              _proto.setActiveDescendant = function setActiveDescendant (activeDescendantID) {
                this.element.setAttribute('aria-activedescendant', activeDescendantID)
              }

              _proto.removeActiveDescendant = function removeActiveDescendant () {
                this.element.removeAttribute('aria-activedescendant')
              }

              _proto._onInput = function _onInput () {
                if (this.type !== SELECT_ONE_TYPE) {
                  this.setWidth()
                }
              }
              /**
               * @param {Event} event
               */

              _proto._onPaste = function _onPaste (event) {
                if (this.preventPaste) {
                  event.preventDefault()
                }
              }

              _proto._onFocus = function _onFocus () {
                this.isFocussed = true
              }

              _proto._onBlur = function _onBlur () {
                this.isFocussed = false
              }

              input_createClass(Input, [
                {
                  key: 'placeholder',
                  set: function set (placeholder) {
                    this.element.placeholder = placeholder
                  }
                  /**
                   * @returns {string}
                   */
                },
                {
                  key: 'value',
                  get: function get () {
                    return sanitise(this.element.value)
                  },
                  /**
                   * @param {string} value
                   */ set: function set (value) {
                    this.element.value = value
                  }
                }
              ])

              return Input
            })()

          // CONCATENATED MODULE: ./src/scripts/components/list.js

          /**
           * @typedef {import('../../../types/index').Choices.Choice} Choice
           */

          let list_List =
            /* #__PURE__ */
            (function () {
              /**
               * @param {{ element: HTMLElement }} args
               */
              function List (_ref) {
                let element = _ref.element
                this.element = element
                this.scrollPos = this.element.scrollTop
                this.height = this.element.offsetHeight
              }

              let _proto = List.prototype

              _proto.clear = function clear () {
                this.element.innerHTML = ''
              }
              /**
               * @param {Element | DocumentFragment} node
               */

              _proto.append = function append (node) {
                this.element.appendChild(node)
              }
              /**
               * @param {string} selector
               * @returns {Element | null}
               */

              _proto.getChild = function getChild (selector) {
                return this.element.querySelector(selector)
              }
              /**
               * @returns {boolean}
               */

              _proto.hasChildren = function hasChildren () {
                return this.element.hasChildNodes()
              }

              _proto.scrollToTop = function scrollToTop () {
                this.element.scrollTop = 0
              }
              /**
               * @param {Element} element
               * @param {1 | -1} direction
               */

              _proto.scrollToChildElement = function scrollToChildElement (element, direction) {
                let _this = this

                if (!element) {
                  return
                }

                let listHeight = this.element.offsetHeight // Scroll position of dropdown

                let listScrollPosition = this.element.scrollTop + listHeight
                let elementHeight = element.offsetHeight // Distance from bottom of element to top of parent

                let elementPos = element.offsetTop + elementHeight // Difference between the element and scroll position

                let destination =
                  direction > 0 ? this.element.scrollTop + elementPos - listScrollPosition : element.offsetTop
                requestAnimationFrame(() => {
                  _this._animateScroll(destination, direction)
                })
              }
              /**
               * @param {number} scrollPos
               * @param {number} strength
               * @param {number} destination
               */

              _proto._scrollDown = function _scrollDown (scrollPos, strength, destination) {
                let easing = (destination - scrollPos) / strength
                let distance = easing > 1 ? easing : 1
                this.element.scrollTop = scrollPos + distance
              }
              /**
               * @param {number} scrollPos
               * @param {number} strength
               * @param {number} destination
               */

              _proto._scrollUp = function _scrollUp (scrollPos, strength, destination) {
                let easing = (scrollPos - destination) / strength
                let distance = easing > 1 ? easing : 1
                this.element.scrollTop = scrollPos - distance
              }
              /**
               * @param {*} destination
               * @param {*} direction
               */

              _proto._animateScroll = function _animateScroll (destination, direction) {
                let _this2 = this

                let strength = SCROLLING_SPEED
                let choiceListScrollTop = this.element.scrollTop
                let continueAnimation = false

                if (direction > 0) {
                  this._scrollDown(choiceListScrollTop, strength, destination)

                  if (choiceListScrollTop < destination) {
                    continueAnimation = true
                  }
                } else {
                  this._scrollUp(choiceListScrollTop, strength, destination)

                  if (choiceListScrollTop > destination) {
                    continueAnimation = true
                  }
                }

                if (continueAnimation) {
                  requestAnimationFrame(() => {
                    _this2._animateScroll(destination, direction)
                  })
                }
              }

              return List
            })()

          // CONCATENATED MODULE: ./src/scripts/components/wrapped-element.js
          function wrapped_element_defineProperties (target, props) {
            for (let i = 0; i < props.length; i++) {
              let descriptor = props[i]
              descriptor.enumerable = descriptor.enumerable || false
              descriptor.configurable = true
              if ('value' in descriptor) descriptor.writable = true
              Object.defineProperty(target, descriptor.key, descriptor)
            }
          }

          function wrapped_element_createClass (Constructor, protoProps, staticProps) {
            if (protoProps) wrapped_element_defineProperties(Constructor.prototype, protoProps)
            if (staticProps) wrapped_element_defineProperties(Constructor, staticProps)
            return Constructor
          }

          /**
           * @typedef {import('../../../types/index').Choices.passedElement} passedElement
           * @typedef {import('../../../types/index').Choices.ClassNames} ClassNames
           */

          let wrapped_element_WrappedElement =
            /* #__PURE__ */
            (function () {
              /**
               * @param {{
               *  element: HTMLInputElement | HTMLSelectElement,
               *  classNames: ClassNames,
               * }} args
               */
              function WrappedElement (_ref) {
                let element = _ref.element
                let classNames = _ref.classNames
                this.element = element
                this.classNames = classNames

                if (!(element instanceof HTMLInputElement) && !(element instanceof HTMLSelectElement)) {
                  throw new TypeError('Invalid element passed')
                }

                this.isDisabled = false
              }

              let _proto = WrappedElement.prototype

              _proto.conceal = function conceal () {
                // Hide passed input
                this.element.classList.add(this.classNames.input)
                this.element.hidden = true // Remove element from tab index

                this.element.tabIndex = -1 // Backup original styles if any

                let origStyle = this.element.getAttribute('style')

                if (origStyle) {
                  this.element.setAttribute('data-choice-orig-style', origStyle)
                }

                this.element.setAttribute('data-choice', 'active')
              }

              _proto.reveal = function reveal () {
                // Reinstate passed element
                this.element.classList.remove(this.classNames.input)
                this.element.hidden = false
                this.element.removeAttribute('tabindex') // Recover original styles if any

                let origStyle = this.element.getAttribute('data-choice-orig-style')

                if (origStyle) {
                  this.element.removeAttribute('data-choice-orig-style')
                  this.element.setAttribute('style', origStyle)
                } else {
                  this.element.removeAttribute('style')
                }

                this.element.removeAttribute('data-choice') // Re-assign values - this is weird, I know
                // @todo Figure out why we need to do this

                this.element.value = this.element.value // eslint-disable-line no-self-assign
              }

              _proto.enable = function enable () {
                this.element.removeAttribute('disabled')
                this.element.disabled = false
                this.isDisabled = false
              }

              _proto.disable = function disable () {
                this.element.setAttribute('disabled', '')
                this.element.disabled = true
                this.isDisabled = true
              }

              _proto.triggerEvent = function triggerEvent (eventType, data) {
                dispatchEvent(this.element, eventType, data)
              }

              wrapped_element_createClass(WrappedElement, [
                {
                  key: 'isActive',
                  get: function get () {
                    return this.element.dataset.choice === 'active'
                  }
                },
                {
                  key: 'dir',
                  get: function get () {
                    return this.element.dir
                  }
                },
                {
                  key: 'value',
                  get: function get () {
                    return this.element.value
                  },
                  set: function set (value) {
                    // you must define setter here otherwise it will be readonly property
                    this.element.value = value
                  }
                }
              ])

              return WrappedElement
            })()

          // CONCATENATED MODULE: ./src/scripts/components/wrapped-input.js
          function wrapped_input_defineProperties (target, props) {
            for (let i = 0; i < props.length; i++) {
              let descriptor = props[i]
              descriptor.enumerable = descriptor.enumerable || false
              descriptor.configurable = true
              if ('value' in descriptor) descriptor.writable = true
              Object.defineProperty(target, descriptor.key, descriptor)
            }
          }

          function wrapped_input_createClass (Constructor, protoProps, staticProps) {
            if (protoProps) wrapped_input_defineProperties(Constructor.prototype, protoProps)
            if (staticProps) wrapped_input_defineProperties(Constructor, staticProps)
            return Constructor
          }

          function _inheritsLoose (subClass, superClass) {
            subClass.prototype = Object.create(superClass.prototype)
            subClass.prototype.constructor = subClass
            subClass.__proto__ = superClass
          }

          /**
           * @typedef {import('../../../types/index').Choices.ClassNames} ClassNames
           * @typedef {import('../../../types/index').Choices.Item} Item
           */

          let WrappedInput =
            /* #__PURE__ */
            (function (_WrappedElement) {
              _inheritsLoose(WrappedInput, _WrappedElement)

              /**
               * @param {{
               *  element: HTMLInputElement,
               *  classNames: ClassNames,
               *  delimiter: string
               * }} args
               */
              function WrappedInput (_ref) {
                let _this

                let element = _ref.element
                let classNames = _ref.classNames
                let delimiter = _ref.delimiter
                _this =
                  _WrappedElement.call(this, {
                    element,
                    classNames
                  }) || this
                _this.delimiter = delimiter
                return _this
              }
              /**
               * @returns {string}
               */

              wrapped_input_createClass(WrappedInput, [
                {
                  key: 'value',
                  get: function get () {
                    return this.element.value
                  },
                  /**
                   * @param {Item[]} items
                   */ set: function set (items) {
                    let itemValues = items.map((_ref2) => {
                      let value = _ref2.value
                      return value
                    })
                    let joinedValues = itemValues.join(this.delimiter)
                    this.element.setAttribute('value', joinedValues)
                    this.element.value = joinedValues
                  }
                }
              ])

              return WrappedInput
            })(wrapped_element_WrappedElement)

          // CONCATENATED MODULE: ./src/scripts/components/wrapped-select.js
          function wrapped_select_defineProperties (target, props) {
            for (let i = 0; i < props.length; i++) {
              let descriptor = props[i]
              descriptor.enumerable = descriptor.enumerable || false
              descriptor.configurable = true
              if ('value' in descriptor) descriptor.writable = true
              Object.defineProperty(target, descriptor.key, descriptor)
            }
          }

          function wrapped_select_createClass (Constructor, protoProps, staticProps) {
            if (protoProps) wrapped_select_defineProperties(Constructor.prototype, protoProps)
            if (staticProps) wrapped_select_defineProperties(Constructor, staticProps)
            return Constructor
          }

          function wrapped_select_inheritsLoose (subClass, superClass) {
            subClass.prototype = Object.create(superClass.prototype)
            subClass.prototype.constructor = subClass
            subClass.__proto__ = superClass
          }

          /**
           * @typedef {import('../../../types/index').Choices.ClassNames} ClassNames
           * @typedef {import('../../../types/index').Choices.Item} Item
           * @typedef {import('../../../types/index').Choices.Choice} Choice
           */

          let WrappedSelect =
            /* #__PURE__ */
            (function (_WrappedElement) {
              wrapped_select_inheritsLoose(WrappedSelect, _WrappedElement)

              /**
               * @param {{
               *  element: HTMLSelectElement,
               *  classNames: ClassNames,
               *  delimiter: string
               *  template: function
               * }} args
               */
              function WrappedSelect (_ref) {
                let _this

                let element = _ref.element
                let classNames = _ref.classNames
                let template = _ref.template
                _this =
                  _WrappedElement.call(this, {
                    element,
                    classNames
                  }) || this
                _this.template = template
                return _this
              }

              let _proto = WrappedSelect.prototype

              /**
               * @param {DocumentFragment} fragment
               */
              _proto.appendDocFragment = function appendDocFragment (fragment) {
                this.element.innerHTML = ''
                this.element.appendChild(fragment)
              }

              wrapped_select_createClass(WrappedSelect, [
                {
                  key: 'placeholderOption',
                  get: function get () {
                    return (
                      this.element.querySelector('option[value=""]') || // Backward compatibility layer for the non-standard placeholder attribute supported in older versions.
                      this.element.querySelector('option[placeholder]')
                    )
                  }
                  /**
                   * @returns {Element[]}
                   */
                },
                {
                  key: 'optionGroups',
                  get: function get () {
                    return Array.from(this.element.getElementsByTagName('OPTGROUP'))
                  }
                  /**
                   * @returns {Item[] | Choice[]}
                   */
                },
                {
                  key: 'options',
                  get: function get () {
                    return Array.from(this.element.options)
                  },
                  /**
                   * @param {Item[] | Choice[]} options
                   */ set: function set (options) {
                    let _this2 = this

                    let fragment = document.createDocumentFragment()

                    let addOptionToFragment = function addOptionToFragment (data) {
                      // Create a standard select option
                      let option = _this2.template(data) // Append it to fragment

                      fragment.appendChild(option)
                    } // Add each list item to list

                    options.forEach((optionData) => {
                      return addOptionToFragment(optionData)
                    })
                    this.appendDocFragment(fragment)
                  }
                }
              ])

              return WrappedSelect
            })(wrapped_element_WrappedElement)

          // CONCATENATED MODULE: ./src/scripts/components/index.js

          // CONCATENATED MODULE: ./src/scripts/templates.js
          /**
           * Helpers to create HTML elements used by Choices
           * Can be overridden by providing `callbackOnCreateTemplates` option
           * @typedef {import('../../types/index').Choices.Templates} Templates
           * @typedef {import('../../types/index').Choices.ClassNames} ClassNames
           * @typedef {import('../../types/index').Choices.Options} Options
           * @typedef {import('../../types/index').Choices.Item} Item
           * @typedef {import('../../types/index').Choices.Choice} Choice
           * @typedef {import('../../types/index').Choices.Group} Group
           */
          let TEMPLATES =
            /** @type {Templates} */
            {
              /**
               * @param {Partial<ClassNames>} classNames
               * @param {"ltr" | "rtl" | "auto"} dir
               * @param {boolean} isSelectElement
               * @param {boolean} isSelectOneElement
               * @param {boolean} searchEnabled
               * @param {"select-one" | "select-multiple" | "text"} passedElementType
               */
              containerOuter: function containerOuter (
                _ref,
                dir,
                isSelectElement,
                isSelectOneElement,
                searchEnabled,
                passedElementType
              ) {
                let _containerOuter = _ref.containerOuter
                let div = Object.assign(document.createElement('div'), {
                  className: _containerOuter
                })
                div.dataset.type = passedElementType

                if (dir) {
                  div.dir = dir
                }

                if (isSelectOneElement) {
                  div.tabIndex = 0
                }

                if (isSelectElement) {
                  div.setAttribute('role', searchEnabled ? 'combobox' : 'listbox')

                  if (searchEnabled) {
                    div.setAttribute('aria-autocomplete', 'list')
                  }
                }

                div.setAttribute('aria-haspopup', 'true')
                div.setAttribute('aria-expanded', 'false')
                return div
              },

              /**
               * @param {Partial<ClassNames>} classNames
               */
              containerInner: function containerInner (_ref2) {
                let _containerInner = _ref2.containerInner
                return Object.assign(document.createElement('div'), {
                  className: _containerInner
                })
              },

              /**
               * @param {Partial<ClassNames>} classNames
               * @param {boolean} isSelectOneElement
               */
              itemList: function itemList (_ref3, isSelectOneElement) {
                let list = _ref3.list
                let listSingle = _ref3.listSingle
                let listItems = _ref3.listItems
                return Object.assign(document.createElement('div'), {
                  className: `${list  } ${  isSelectOneElement ? listSingle : listItems}`
                })
              },

              /**
               * @param {Partial<ClassNames>} classNames
               * @param {string} value
               */
              placeholder: function placeholder (_ref4, value) {
                let _placeholder = _ref4.placeholder
                return Object.assign(document.createElement('div'), {
                  className: _placeholder,
                  innerHTML: value
                })
              },

              /**
               * @param {Partial<ClassNames>} classNames
               * @param {Item} item
               * @param {boolean} removeItemButton
               */
              item: function item (_ref5, _ref6, removeItemButton) {
                let _item = _ref5.item
                let button = _ref5.button
                let highlightedState = _ref5.highlightedState
                let itemSelectable = _ref5.itemSelectable
                let placeholder = _ref5.placeholder
                let id = _ref6.id
                let value = _ref6.value
                let label = _ref6.label
                let customProperties = _ref6.customProperties
                let active = _ref6.active
                let disabled = _ref6.disabled
                let highlighted = _ref6.highlighted
                let isPlaceholder = _ref6.placeholder
                let div = Object.assign(document.createElement('div'), {
                  className: _item,
                  innerHTML: label
                })
                Object.assign(div.dataset, {
                  item: '',
                  id,
                  value,
                  customProperties
                })

                if (active) {
                  div.setAttribute('aria-selected', 'true')
                }

                if (disabled) {
                  div.setAttribute('aria-disabled', 'true')
                }

                if (isPlaceholder) {
                  div.classList.add(placeholder)
                }

                div.classList.add(highlighted ? highlightedState : itemSelectable)

                if (removeItemButton) {
                  if (disabled) {
                    div.classList.remove(itemSelectable)
                  }

                  div.dataset.deletable = ''
                  /** @todo This MUST be localizable, not hardcoded! */

                  let REMOVE_ITEM_TEXT = 'Remove item'
                  let removeButton = Object.assign(document.createElement('button'), {
                    type: 'button',
                    className: button,
                    innerHTML: REMOVE_ITEM_TEXT
                  })
                  removeButton.setAttribute('aria-label', `${REMOVE_ITEM_TEXT  }: '${  value  }'`)
                  removeButton.dataset.button = ''
                  div.appendChild(removeButton)
                }

                return div
              },

              /**
               * @param {Partial<ClassNames>} classNames
               * @param {boolean} isSelectOneElement
               */
              choiceList: function choiceList (_ref7, isSelectOneElement) {
                let list = _ref7.list
                let div = Object.assign(document.createElement('div'), {
                  className: list
                })

                if (!isSelectOneElement) {
                  div.setAttribute('aria-multiselectable', 'true')
                }

                div.setAttribute('role', 'listbox')
                return div
              },

              /**
               * @param {Partial<ClassNames>} classNames
               * @param {Group} group
               */
              choiceGroup: function choiceGroup (_ref8, _ref9) {
                let group = _ref8.group
                let groupHeading = _ref8.groupHeading
                let itemDisabled = _ref8.itemDisabled
                let id = _ref9.id
                let value = _ref9.value
                let disabled = _ref9.disabled
                let div = Object.assign(document.createElement('div'), {
                  className: `${group  } ${  disabled ? itemDisabled : ''}`
                })
                div.setAttribute('role', 'group')
                Object.assign(div.dataset, {
                  group: '',
                  id,
                  value
                })

                if (disabled) {
                  div.setAttribute('aria-disabled', 'true')
                }

                div.appendChild(
                  Object.assign(document.createElement('div'), {
                    className: groupHeading,
                    innerHTML: value
                  })
                )
                return div
              },

              /**
               * @param {Partial<ClassNames>} classNames
               * @param {Choice} choice
               * @param {Options['itemSelectText']} selectText
               */
              choice: function choice (_ref10, _ref11, selectText) {
                let item = _ref10.item
                let itemChoice = _ref10.itemChoice
                let itemSelectable = _ref10.itemSelectable
                let selectedState = _ref10.selectedState
                let itemDisabled = _ref10.itemDisabled
                let placeholder = _ref10.placeholder
                let id = _ref11.id
                let value = _ref11.value
                let label = _ref11.label
                let groupId = _ref11.groupId
                let elementId = _ref11.elementId
                let isDisabled = _ref11.disabled
                let isSelected = _ref11.selected
                let isPlaceholder = _ref11.placeholder
                let div = Object.assign(document.createElement('div'), {
                  id: elementId,
                  innerHTML: label,
                  className: `${item  } ${  itemChoice}`
                })

                if (isSelected) {
                  div.classList.add(selectedState)
                }

                if (isPlaceholder) {
                  div.classList.add(placeholder)
                }

                div.setAttribute('role', groupId > 0 ? 'treeitem' : 'option')
                Object.assign(div.dataset, {
                  choice: '',
                  id,
                  value,
                  selectText
                })

                if (isDisabled) {
                  div.classList.add(itemDisabled)
                  div.dataset.choiceDisabled = ''
                  div.setAttribute('aria-disabled', 'true')
                } else {
                  div.classList.add(itemSelectable)
                  div.dataset.choiceSelectable = ''
                }

                return div
              },

              /**
               * @param {Partial<ClassNames>} classNames
               * @param {string} placeholderValue
               */
              input: function input (_ref12, placeholderValue) {
                let _input = _ref12.input
                let inputCloned = _ref12.inputCloned
                let inp = Object.assign(document.createElement('input'), {
                  type: 'text',
                  className: `${_input  } ${  inputCloned}`,
                  autocomplete: 'off',
                  autocapitalize: 'off',
                  spellcheck: false
                })
                inp.setAttribute('role', 'textbox')
                inp.setAttribute('aria-autocomplete', 'list')
                inp.setAttribute('aria-label', placeholderValue)
                return inp
              },

              /**
               * @param {Partial<ClassNames>} classNames
               */
              dropdown: function dropdown (_ref13) {
                let list = _ref13.list
                let listDropdown = _ref13.listDropdown
                let div = document.createElement('div')
                div.classList.add(list, listDropdown)
                div.setAttribute('aria-expanded', 'false')
                return div
              },

              /**
               *
               * @param {Partial<ClassNames>} classNames
               * @param {string} innerHTML
               * @param {"no-choices" | "no-results" | ""} type
               */
              notice: function notice (_ref14, innerHTML, type) {
                let item = _ref14.item
                let itemChoice = _ref14.itemChoice
                let noResults = _ref14.noResults
                let noChoices = _ref14.noChoices

                if (type === void 0) {
                  type = ''
                }

                let classes = [item, itemChoice]

                if (type === 'no-choices') {
                  classes.push(noChoices)
                } else if (type === 'no-results') {
                  classes.push(noResults)
                }

                return Object.assign(document.createElement('div'), {
                  innerHTML,
                  className: classes.join(' ')
                })
              },

              /**
               * @param {Item} option
               */
              option: function option (_ref15) {
                let label = _ref15.label
                let value = _ref15.value
                let customProperties = _ref15.customProperties
                let active = _ref15.active
                let disabled = _ref15.disabled
                let opt = new Option(label, value, false, active)

                if (customProperties) {
                  opt.dataset.customProperties = customProperties
                }

                opt.disabled = disabled
                return opt
              }
            }
          // CONCATENATED MODULE: ./src/scripts/actions/choices.js
          /**
           * @typedef {import('redux').Action} Action
           * @typedef {import('../../../types/index').Choices.Choice} Choice
           */

          /**
           * @argument {Choice} choice
           * @returns {Action & Choice}
           */

          let choices_addChoice = function addChoice (_ref) {
            let value = _ref.value
            let label = _ref.label
            let id = _ref.id
            let groupId = _ref.groupId
            let disabled = _ref.disabled
            let elementId = _ref.elementId
            let customProperties = _ref.customProperties
            let placeholder = _ref.placeholder
            let keyCode = _ref.keyCode
            return {
              type: ACTION_TYPES.ADD_CHOICE,
              value,
              label,
              id,
              groupId,
              disabled,
              elementId,
              customProperties,
              placeholder,
              keyCode
            }
          }
          /**
           * @argument {Choice[]} results
           * @returns {Action & { results: Choice[] }}
           */

          let choices_filterChoices = function filterChoices (results) {
            return {
              type: ACTION_TYPES.FILTER_CHOICES,
              results
            }
          }
          /**
           * @argument {boolean} active
           * @returns {Action & { active: boolean }}
           */

          let choices_activateChoices = function activateChoices (active) {
            if (active === void 0) {
              active = true
            }

            return {
              type: ACTION_TYPES.ACTIVATE_CHOICES,
              active
            }
          }
          /**
           * @returns {Action}
           */

          let choices_clearChoices = function clearChoices () {
            return {
              type: ACTION_TYPES.CLEAR_CHOICES
            }
          }
          // CONCATENATED MODULE: ./src/scripts/actions/items.js

          /**
           * @typedef {import('redux').Action} Action
           * @typedef {import('../../../types/index').Choices.Item} Item
           */

          /**
           * @param {Item} item
           * @returns {Action & Item}
           */

          let items_addItem = function addItem (_ref) {
            let value = _ref.value
            let label = _ref.label
            let id = _ref.id
            let choiceId = _ref.choiceId
            let groupId = _ref.groupId
            let customProperties = _ref.customProperties
            let placeholder = _ref.placeholder
            let keyCode = _ref.keyCode
            return {
              type: ACTION_TYPES.ADD_ITEM,
              value,
              label,
              id,
              choiceId,
              groupId,
              customProperties,
              placeholder,
              keyCode
            }
          }
          /**
           * @param {string} id
           * @param {string} choiceId
           * @returns {Action & { id: string, choiceId: string }}
           */

          let items_removeItem = function removeItem (id, choiceId) {
            return {
              type: ACTION_TYPES.REMOVE_ITEM,
              id,
              choiceId
            }
          }
          /**
           * @param {string} id
           * @param {boolean} highlighted
           * @returns {Action & { id: string, highlighted: boolean }}
           */

          let items_highlightItem = function highlightItem (id, highlighted) {
            return {
              type: ACTION_TYPES.HIGHLIGHT_ITEM,
              id,
              highlighted
            }
          }
          // CONCATENATED MODULE: ./src/scripts/actions/groups.js

          /**
           * @typedef {import('redux').Action} Action
           * @typedef {import('../../../types/index').Choices.Group} Group
           */

          /**
           * @param {Group} group
           * @returns {Action & Group}
           */

          let groups_addGroup = function addGroup (_ref) {
            let value = _ref.value
            let id = _ref.id
            let active = _ref.active
            let disabled = _ref.disabled
            return {
              type: ACTION_TYPES.ADD_GROUP,
              value,
              id,
              active,
              disabled
            }
          }
          // CONCATENATED MODULE: ./src/scripts/actions/misc.js
          /**
           * @typedef {import('redux').Action} Action
           */

          /**
           * @returns {Action}
           */
          let clearAll = function clearAll () {
            return {
              type: 'CLEAR_ALL'
            }
          }
          /**
           * @param {any} state
           * @returns {Action & { state: object }}
           */

          let resetTo = function resetTo (state) {
            return {
              type: 'RESET_TO',
              state
            }
          }
          /**
           * @param {boolean} isLoading
           * @returns {Action & { isLoading: boolean }}
           */

          let setIsLoading = function setIsLoading (isLoading) {
            return {
              type: 'SET_IS_LOADING',
              isLoading
            }
          }
          // CONCATENATED MODULE: ./src/scripts/choices.js
          function choices_defineProperties (target, props) {
            for (let i = 0; i < props.length; i++) {
              let descriptor = props[i]
              descriptor.enumerable = descriptor.enumerable || false
              descriptor.configurable = true
              if ('value' in descriptor) descriptor.writable = true
              Object.defineProperty(target, descriptor.key, descriptor)
            }
          }

          function choices_createClass (Constructor, protoProps, staticProps) {
            if (protoProps) choices_defineProperties(Constructor.prototype, protoProps)
            if (staticProps) choices_defineProperties(Constructor, staticProps)
            return Constructor
          }

          /** @see {@link http://browserhacks.com/#hack-acea075d0ac6954f275a70023906050c} */

          let IS_IE11 =
            '-ms-scroll-limit' in document.documentElement.style && '-ms-ime-align' in document.documentElement.style
          /**
           * @typedef {import('../../types/index').Choices.Choice} Choice
           * @typedef {import('../../types/index').Choices.Item} Item
           * @typedef {import('../../types/index').Choices.Group} Group
           * @typedef {import('../../types/index').Choices.Options} Options
           */

          /** @type {Partial<Options>} */

          let USER_DEFAULTS = {}
          /**
           * Choices
           * @author Josh Johnson<josh@joshuajohnson.co.uk>
           */

          let choices_Choices =
            /* #__PURE__ */
            (function () {
              choices_createClass(Choices, null, [
                {
                  key: 'defaults',
                  get: function get () {
                    return Object.preventExtensions({
                      get options () {
                        return USER_DEFAULTS
                      },

                      get templates () {
                        return TEMPLATES
                      }
                    })
                  }
                  /**
                   * @param {string | HTMLInputElement | HTMLSelectElement} element
                   * @param {Partial<Options>} userConfig
                   */
                }
              ])

              function Choices (element, userConfig) {
                let _this = this

                if (element === void 0) {
                  element = '[data-choice]'
                }

                if (userConfig === void 0) {
                  userConfig = {}
                }

                /** @type {Partial<Options>} */
                this.config = cjs_default.a.all(
                  [DEFAULT_CONFIG, Choices.defaults.options, userConfig], // When merging array configs, replace with a copy of the userConfig array,
                  // instead of concatenating with the default array
                  {
                    arrayMerge: function arrayMerge (_, sourceArray) {
                      return [].concat(sourceArray)
                    }
                  }
                )
                let invalidConfigOptions = diff(this.config, DEFAULT_CONFIG)

                if (invalidConfigOptions.length) {
                  console.warn('Unknown config option(s) passed', invalidConfigOptions.join(', '))
                }

                let passedElement = typeof element === 'string' ? document.querySelector(element) : element

                if (!(passedElement instanceof HTMLInputElement || passedElement instanceof HTMLSelectElement)) {
                  throw TypeError('Expected one of the following types text|select-one|select-multiple')
                }

                this._isTextElement = passedElement.type === TEXT_TYPE
                this._isSelectOneElement = passedElement.type === SELECT_ONE_TYPE
                this._isSelectMultipleElement = passedElement.type === SELECT_MULTIPLE_TYPE
                this._isSelectElement = this._isSelectOneElement || this._isSelectMultipleElement
                this.config.searchEnabled = this._isSelectMultipleElement || this.config.searchEnabled

                if (!['auto', 'always'].includes(this.config.renderSelectedChoices)) {
                  this.config.renderSelectedChoices = 'auto'
                }

                if (userConfig.addItemFilter && typeof userConfig.addItemFilter !== 'function') {
                  let re =
                    userConfig.addItemFilter instanceof RegExp
                      ? userConfig.addItemFilter
                      : new RegExp(userConfig.addItemFilter)
                  this.config.addItemFilter = re.test.bind(re)
                }

                if (this._isTextElement) {
                  this.passedElement = new WrappedInput({
                    element: passedElement,
                    classNames: this.config.classNames,
                    delimiter: this.config.delimiter
                  })
                } else {
                  this.passedElement = new WrappedSelect({
                    element: passedElement,
                    classNames: this.config.classNames,
                    template: function template (data) {
                      return _this._templates.option(data)
                    }
                  })
                }

                this.initialised = false
                this._store = new store_Store()
                this._initialState = {}
                this._currentState = {}
                this._prevState = {}
                this._currentValue = ''
                this._canSearch = this.config.searchEnabled
                this._isScrollingOnIe = false
                this._highlightPosition = 0
                this._wasTap = true
                this._placeholderValue = this._generatePlaceholderValue()
                this._baseId = generateId(this.passedElement.element, 'choices-')
                /**
                 * setting direction in cases where it's explicitly set on passedElement
                 * or when calculated direction is different from the document
                 * @type {HTMLElement['dir']}
                 */

                this._direction = this.passedElement.dir

                if (!this._direction) {
                  let _window$getComputedSt = window.getComputedStyle(this.passedElement.element)
                  let elementDirection = _window$getComputedSt.direction

                  let _window$getComputedSt2 = window.getComputedStyle(document.documentElement)
                  let documentDirection = _window$getComputedSt2.direction

                  if (elementDirection !== documentDirection) {
                    this._direction = elementDirection
                  }
                }

                this._idNames = {
                  itemChoice: 'item-choice'
                } // Assign preset groups from passed element

                this._presetGroups = this.passedElement.optionGroups // Assign preset options from passed element

                this._presetOptions = this.passedElement.options // Assign preset choices from passed object

                this._presetChoices = this.config.choices // Assign preset items from passed object first

                this._presetItems = this.config.items // Add any values passed from attribute

                if (this.passedElement.value) {
                  this._presetItems = this._presetItems.concat(this.passedElement.value.split(this.config.delimiter))
                } // Create array of choices from option elements

                if (this.passedElement.options) {
                  this.passedElement.options.forEach((o) => {
                    _this._presetChoices.push({
                      value: o.value,
                      label: o.innerHTML,
                      selected: o.selected,
                      disabled: o.disabled || o.parentNode.disabled,
                      placeholder: o.value === '' || o.hasAttribute('placeholder'),
                      customProperties: o.getAttribute('data-custom-properties')
                    })
                  })
                }

                this._render = this._render.bind(this)
                this._onFocus = this._onFocus.bind(this)
                this._onBlur = this._onBlur.bind(this)
                this._onKeyUp = this._onKeyUp.bind(this)
                this._onKeyDown = this._onKeyDown.bind(this)
                this._onClick = this._onClick.bind(this)
                this._onTouchMove = this._onTouchMove.bind(this)
                this._onTouchEnd = this._onTouchEnd.bind(this)
                this._onMouseDown = this._onMouseDown.bind(this)
                this._onMouseOver = this._onMouseOver.bind(this)
                this._onFormReset = this._onFormReset.bind(this)
                this._onAKey = this._onAKey.bind(this)
                this._onEnterKey = this._onEnterKey.bind(this)
                this._onEscapeKey = this._onEscapeKey.bind(this)
                this._onDirectionKey = this._onDirectionKey.bind(this)
                this._onDeleteKey = this._onDeleteKey.bind(this) // If element has already been initialised with Choices, fail silently

                if (this.passedElement.isActive) {
                  if (!this.config.silent) {
                    console.warn('Trying to initialise Choices on element already initialised')
                  }

                  this.initialised = true
                  return
                } // Let's go

                this.init()
              }

              let _proto = Choices.prototype

              _proto.init = function init () {
                if (this.initialised) {
                  return
                }

                this._createTemplates()

                this._createElements()

                this._createStructure() // Set initial state (We need to clone the state because some reducers
                // modify the inner objects properties in the state) 🤢

                this._initialState = cloneObject(this._store.state)

                this._store.subscribe(this._render)

                this._render()

                this._addEventListeners()

                let shouldDisable = !this.config.addItems || this.passedElement.element.hasAttribute('disabled')

                if (shouldDisable) {
                  this.disable()
                }

                this.initialised = true
                let callbackOnInit = this.config.callbackOnInit // Run callback if it is a function

                if (callbackOnInit && typeof callbackOnInit === 'function') {
                  callbackOnInit.call(this)
                }
              }

              _proto.destroy = function destroy () {
                if (!this.initialised) {
                  return
                }

                this._removeEventListeners()

                this.passedElement.reveal()
                this.containerOuter.unwrap(this.passedElement.element)
                this.clearStore()

                if (this._isSelectElement) {
                  this.passedElement.options = this._presetOptions
                }

                this._templates = null
                this.initialised = false
              }

              _proto.enable = function enable () {
                if (this.passedElement.isDisabled) {
                  this.passedElement.enable()
                }

                if (this.containerOuter.isDisabled) {
                  this._addEventListeners()

                  this.input.enable()
                  this.containerOuter.enable()
                }

                return this
              }

              _proto.disable = function disable () {
                if (!this.passedElement.isDisabled) {
                  this.passedElement.disable()
                }

                if (!this.containerOuter.isDisabled) {
                  this._removeEventListeners()

                  this.input.disable()
                  this.containerOuter.disable()
                }

                return this
              }

              _proto.highlightItem = function highlightItem (item, runEvent) {
                if (runEvent === void 0) {
                  runEvent = true
                }

                if (!item) {
                  return this
                }

                let id = item.id
                let _item$groupId = item.groupId
                let groupId = _item$groupId === void 0 ? -1 : _item$groupId
                let _item$value = item.value
                let value = _item$value === void 0 ? '' : _item$value
                let _item$label = item.label
                let label = _item$label === void 0 ? '' : _item$label
                let group = groupId >= 0 ? this._store.getGroupById(groupId) : null

                this._store.dispatch(items_highlightItem(id, true))

                if (runEvent) {
                  this.passedElement.triggerEvent(EVENTS.highlightItem, {
                    id,
                    value,
                    label,
                    groupValue: group && group.value ? group.value : null
                  })
                }

                return this
              }

              _proto.unhighlightItem = function unhighlightItem (item) {
                if (!item) {
                  return this
                }

                let id = item.id
                let _item$groupId2 = item.groupId
                let groupId = _item$groupId2 === void 0 ? -1 : _item$groupId2
                let _item$value2 = item.value
                let value = _item$value2 === void 0 ? '' : _item$value2
                let _item$label2 = item.label
                let label = _item$label2 === void 0 ? '' : _item$label2
                let group = groupId >= 0 ? this._store.getGroupById(groupId) : null

                this._store.dispatch(items_highlightItem(id, false))

                this.passedElement.triggerEvent(EVENTS.highlightItem, {
                  id,
                  value,
                  label,
                  groupValue: group && group.value ? group.value : null
                })
                return this
              }

              _proto.highlightAll = function highlightAll () {
                let _this2 = this

                this._store.items.forEach((item) => {
                  return _this2.highlightItem(item)
                })

                return this
              }

              _proto.unhighlightAll = function unhighlightAll () {
                let _this3 = this

                this._store.items.forEach((item) => {
                  return _this3.unhighlightItem(item)
                })

                return this
              }

              _proto.removeActiveItemsByValue = function removeActiveItemsByValue (value) {
                let _this4 = this

                this._store.activeItems
                  .filter((item) => {
                    return item.value === value
                  })
                  .forEach((item) => {
                    return _this4._removeItem(item)
                  })

                return this
              }

              _proto.removeActiveItems = function removeActiveItems (excludedId) {
                let _this5 = this

                this._store.activeItems
                  .filter((_ref) => {
                    let id = _ref.id
                    return id !== excludedId
                  })
                  .forEach((item) => {
                    return _this5._removeItem(item)
                  })

                return this
              }

              _proto.removeHighlightedItems = function removeHighlightedItems (runEvent) {
                let _this6 = this

                if (runEvent === void 0) {
                  runEvent = false
                }

                this._store.highlightedActiveItems.forEach((item) => {
                  _this6._removeItem(item) // If this action was performed by the user
                  // trigger the event

                  if (runEvent) {
                    _this6._triggerChange(item.value)
                  }
                })

                return this
              }

              _proto.showDropdown = function showDropdown (preventInputFocus) {
                let _this7 = this

                if (this.dropdown.isActive) {
                  return this
                }

                requestAnimationFrame(() => {
                  _this7.dropdown.show()

                  _this7.containerOuter.open(_this7.dropdown.distanceFromTopWindow)

                  if (!preventInputFocus && _this7._canSearch) {
                    _this7.input.focus()
                  }

                  _this7.passedElement.triggerEvent(EVENTS.showDropdown, {})
                })
                return this
              }

              _proto.hideDropdown = function hideDropdown (preventInputBlur) {
                let _this8 = this

                if (!this.dropdown.isActive) {
                  return this
                }

                requestAnimationFrame(() => {
                  _this8.dropdown.hide()

                  _this8.containerOuter.close()

                  if (!preventInputBlur && _this8._canSearch) {
                    _this8.input.removeActiveDescendant()

                    _this8.input.blur()
                  }

                  _this8.passedElement.triggerEvent(EVENTS.hideDropdown, {})
                })
                return this
              }

              _proto.getValue = function getValue (valueOnly) {
                if (valueOnly === void 0) {
                  valueOnly = false
                }

                let values = this._store.activeItems.reduce((selectedItems, item) => {
                  let itemValue = valueOnly ? item.value : item
                  selectedItems.push(itemValue)
                  return selectedItems
                }, [])

                return this._isSelectOneElement ? values[0] : values
              }
              /**
               * @param {string[] | import('../../types/index').Choices.Item[]} items
               */

              _proto.setValue = function setValue (items) {
                let _this9 = this

                if (!this.initialised) {
                  return this
                }

                items.forEach((value) => {
                  return _this9._setChoiceOrItem(value)
                })
                return this
              }

              _proto.setChoiceByValue = function setChoiceByValue (value) {
                let _this10 = this

                if (!this.initialised || this._isTextElement) {
                  return this
                } // If only one value has been passed, convert to array

                let choiceValue = Array.isArray(value) ? value : [value] // Loop through each value and

                choiceValue.forEach((val) => {
                  return _this10._findAndSelectChoiceByValue(val)
                })
                return this
              }
              /**
               * Set choices of select input via an array of objects (or function that returns array of object or promise of it),
               * a value field name and a label field name.
               * This behaves the same as passing items via the choices option but can be called after initialising Choices.
               * This can also be used to add groups of choices (see example 2); Optionally pass a true `replaceChoices` value to remove any existing choices.
               * Optionally pass a `customProperties` object to add additional data to your choices (useful when searching/filtering etc).
               *
               * **Input types affected:** select-one, select-multiple
               *
               * @template {Choice[] | ((instance: Choices) => object[] | Promise<object[]>)} T
               * @param {T} [choicesArrayOrFetcher]
               * @param {string} [value = 'value'] - name of `value` field
               * @param {string} [label = 'label'] - name of 'label' field
               * @param {boolean} [replaceChoices = false] - whether to replace of add choices
               * @returns {this | Promise<this>}
               *
               * @example
               * ```js
               * const example = new Choices(element);
               *
               * example.setChoices([
               *   {value: 'One', label: 'Label One', disabled: true},
               *   {value: 'Two', label: 'Label Two', selected: true},
               *   {value: 'Three', label: 'Label Three'},
               * ], 'value', 'label', false);
               * ```
               *
               * @example
               * ```js
               * const example = new Choices(element);
               *
               * example.setChoices(async () => {
               *   try {
               *      const items = await fetch('/items');
               *      return items.json()
               *   } catch(err) {
               *      console.error(err)
               *   }
               * });
               * ```
               *
               * @example
               * ```js
               * const example = new Choices(element);
               *
               * example.setChoices([{
               *   label: 'Group one',
               *   id: 1,
               *   disabled: false,
               *   choices: [
               *     {value: 'Child One', label: 'Child One', selected: true},
               *     {value: 'Child Two', label: 'Child Two',  disabled: true},
               *     {value: 'Child Three', label: 'Child Three'},
               *   ]
               * },
               * {
               *   label: 'Group two',
               *   id: 2,
               *   disabled: false,
               *   choices: [
               *     {value: 'Child Four', label: 'Child Four', disabled: true},
               *     {value: 'Child Five', label: 'Child Five'},
               *     {value: 'Child Six', label: 'Child Six', customProperties: {
               *       description: 'Custom description about child six',
               *       random: 'Another random custom property'
               *     }},
               *   ]
               * }], 'value', 'label', false);
               * ```
               */

              _proto.setChoices = function setChoices (choicesArrayOrFetcher, value, label, replaceChoices) {
                let _this11 = this

                if (choicesArrayOrFetcher === void 0) {
                  choicesArrayOrFetcher = []
                }

                if (value === void 0) {
                  value = 'value'
                }

                if (label === void 0) {
                  label = 'label'
                }

                if (replaceChoices === void 0) {
                  replaceChoices = false
                }

                if (!this.initialised) {
                  throw new ReferenceError('setChoices was called on a non-initialized instance of Choices')
                }

                if (!this._isSelectElement) {
                  throw new TypeError("setChoices can't be used with INPUT based Choices")
                }

                if (typeof value !== 'string' || !value) {
                  throw new TypeError("value parameter must be a name of 'value' field in passed objects")
                } // Clear choices if needed

                if (replaceChoices) {
                  this.clearChoices()
                }

                if (typeof choicesArrayOrFetcher === 'function') {
                  // it's a choices fetcher function
                  let fetcher = choicesArrayOrFetcher(this)

                  if (typeof Promise === 'function' && fetcher instanceof Promise) {
                    // that's a promise
                    // eslint-disable-next-line compat/compat
                    return new Promise(((resolve) => {
                      return requestAnimationFrame(resolve)
                    }))
                      .then(() => {
                        return _this11._handleLoadingState(true)
                      })
                      .then(() => {
                        return fetcher
                      })
                      .then((data) => {
                        return _this11.setChoices(data, value, label, replaceChoices)
                      })
                      .catch((err) => {
                        if (!_this11.config.silent) {
                          console.error(err)
                        }
                      })
                      .then(() => {
                        return _this11._handleLoadingState(false)
                      })
                      .then(() => {
                        return _this11
                      })
                  } // function returned something else than promise, let's check if it's an array of choices

                  if (!Array.isArray(fetcher)) {
                    throw new TypeError(
                      `.setChoices first argument function must return either array of choices or Promise, got: ${ 
                        typeof fetcher}`
                    )
                  } // recursion with results, it's sync and choices were cleared already

                  return this.setChoices(fetcher, value, label, false)
                }

                if (!Array.isArray(choicesArrayOrFetcher)) {
                  throw new TypeError(
                    '.setChoices must be called either with array of choices with a function resulting into Promise of array of choices'
                  )
                }

                this.containerOuter.removeLoadingState()

                this._startLoading()

                choicesArrayOrFetcher.forEach((groupOrChoice) => {
                  if (groupOrChoice.choices) {
                    _this11._addGroup({
                      id: parseInt(groupOrChoice.id, 10) || null,
                      group: groupOrChoice,
                      valueKey: value,
                      labelKey: label
                    })
                  } else {
                    _this11._addChoice({
                      value: groupOrChoice[value],
                      label: groupOrChoice[label],
                      isSelected: groupOrChoice.selected,
                      isDisabled: groupOrChoice.disabled,
                      customProperties: groupOrChoice.customProperties,
                      placeholder: groupOrChoice.placeholder
                    })
                  }
                })

                this._stopLoading()

                return this
              }

              _proto.clearChoices = function clearChoices () {
                this._store.dispatch(choices_clearChoices())

                return this
              }

              _proto.clearStore = function clearStore () {
                this._store.dispatch(clearAll())

                return this
              }

              _proto.clearInput = function clearInput () {
                let shouldSetInputWidth = !this._isSelectOneElement
                this.input.clear(shouldSetInputWidth)

                if (!this._isTextElement && this._canSearch) {
                  this._isSearching = false

                  this._store.dispatch(choices_activateChoices(true))
                }

                return this
              }

              _proto._render = function _render () {
                if (this._store.isLoading()) {
                  return
                }

                this._currentState = this._store.state
                let stateChanged =
                  this._currentState.choices !== this._prevState.choices ||
                  this._currentState.groups !== this._prevState.groups ||
                  this._currentState.items !== this._prevState.items
                let shouldRenderChoices = this._isSelectElement
                let shouldRenderItems = this._currentState.items !== this._prevState.items

                if (!stateChanged) {
                  return
                }

                if (shouldRenderChoices) {
                  this._renderChoices()
                }

                if (shouldRenderItems) {
                  this._renderItems()
                }

                this._prevState = this._currentState
              }

              _proto._renderChoices = function _renderChoices () {
                let _this12 = this

                let _this$_store = this._store
                let activeGroups = _this$_store.activeGroups
                let activeChoices = _this$_store.activeChoices
                let choiceListFragment = document.createDocumentFragment()
                this.choiceList.clear()

                if (this.config.resetScrollPosition) {
                  requestAnimationFrame(() => {
                    return _this12.choiceList.scrollToTop()
                  })
                } // If we have grouped options

                if (activeGroups.length >= 1 && !this._isSearching) {
                  // If we have a placeholder choice along with groups
                  let activePlaceholders = activeChoices.filter((activeChoice) => {
                    return activeChoice.placeholder === true && activeChoice.groupId === -1
                  })

                  if (activePlaceholders.length >= 1) {
                    choiceListFragment = this._createChoicesFragment(activePlaceholders, choiceListFragment)
                  }

                  choiceListFragment = this._createGroupsFragment(activeGroups, activeChoices, choiceListFragment)
                } else if (activeChoices.length >= 1) {
                  choiceListFragment = this._createChoicesFragment(activeChoices, choiceListFragment)
                } // If we have choices to show

                if (choiceListFragment.childNodes && choiceListFragment.childNodes.length > 0) {
                  let activeItems = this._store.activeItems

                  let canAddItem = this._canAddItem(activeItems, this.input.value) // ...and we can select them

                  if (canAddItem.response) {
                    // ...append them and highlight the first choice
                    this.choiceList.append(choiceListFragment)

                    this._highlightChoice()
                  } else {
                    // ...otherwise show a notice
                    this.choiceList.append(this._getTemplate('notice', canAddItem.notice))
                  }
                } else {
                  // Otherwise show a notice
                  let dropdownItem
                  let notice

                  if (this._isSearching) {
                    notice =
                      typeof this.config.noResultsText === 'function'
                        ? this.config.noResultsText()
                        : this.config.noResultsText
                    dropdownItem = this._getTemplate('notice', notice, 'no-results')
                  } else {
                    notice =
                      typeof this.config.noChoicesText === 'function'
                        ? this.config.noChoicesText()
                        : this.config.noChoicesText
                    dropdownItem = this._getTemplate('notice', notice, 'no-choices')
                  }

                  this.choiceList.append(dropdownItem)
                }
              }

              _proto._renderItems = function _renderItems () {
                let activeItems = this._store.activeItems || []
                this.itemList.clear() // Create a fragment to store our list items
                // (so we don't have to update the DOM for each item)

                let itemListFragment = this._createItemsFragment(activeItems) // If we have items to add, append them

                if (itemListFragment.childNodes) {
                  this.itemList.append(itemListFragment)
                }
              }

              _proto._createGroupsFragment = function _createGroupsFragment (groups, choices, fragment) {
                let _this13 = this

                if (fragment === void 0) {
                  fragment = document.createDocumentFragment()
                }

                let getGroupChoices = function getGroupChoices (group) {
                  return choices.filter((choice) => {
                    if (_this13._isSelectOneElement) {
                      return choice.groupId === group.id
                    }

                    return (
                      choice.groupId === group.id &&
                      (_this13.config.renderSelectedChoices === 'always' || !choice.selected)
                    )
                  })
                } // If sorting is enabled, filter groups

                if (this.config.shouldSort) {
                  groups.sort(this.config.sorter)
                }

                groups.forEach((group) => {
                  let groupChoices = getGroupChoices(group)

                  if (groupChoices.length >= 1) {
                    let dropdownGroup = _this13._getTemplate('choiceGroup', group)

                    fragment.appendChild(dropdownGroup)

                    _this13._createChoicesFragment(groupChoices, fragment, true)
                  }
                })
                return fragment
              }

              _proto._createChoicesFragment = function _createChoicesFragment (choices, fragment, withinGroup) {
                let _this14 = this

                if (fragment === void 0) {
                  fragment = document.createDocumentFragment()
                }

                if (withinGroup === void 0) {
                  withinGroup = false
                }

                // Create a fragment to store our list items (so we don't have to update the DOM for each item)
                let _this$config = this.config
                let renderSelectedChoices = _this$config.renderSelectedChoices
                let searchResultLimit = _this$config.searchResultLimit
                let renderChoiceLimit = _this$config.renderChoiceLimit
                let filter = this._isSearching ? sortByScore : this.config.sorter

                let appendChoice = function appendChoice (choice) {
                  let shouldRender =
                    renderSelectedChoices === 'auto' ? _this14._isSelectOneElement || !choice.selected : true

                  if (shouldRender) {
                    let dropdownItem = _this14._getTemplate('choice', choice, _this14.config.itemSelectText)

                    fragment.appendChild(dropdownItem)
                  }
                }

                let rendererableChoices = choices

                if (renderSelectedChoices === 'auto' && !this._isSelectOneElement) {
                  rendererableChoices = choices.filter((choice) => {
                    return !choice.selected
                  })
                } // Split array into placeholders and "normal" choices

                let _rendererableChoices$ = rendererableChoices.reduce(
                  (acc, choice) => {
                    if (choice.placeholder) {
                      acc.placeholderChoices.push(choice)
                    } else {
                      acc.normalChoices.push(choice)
                    }

                    return acc
                  },
                  {
                    placeholderChoices: [],
                    normalChoices: []
                  }
                )
                let placeholderChoices = _rendererableChoices$.placeholderChoices
                let normalChoices = _rendererableChoices$.normalChoices // If sorting is enabled or the user is searching, filter choices

                if (this.config.shouldSort || this._isSearching) {
                  normalChoices.sort(filter)
                }

                let choiceLimit = rendererableChoices.length // Prepend placeholeder

                let sortedChoices = this._isSelectOneElement
                  ? [].concat(placeholderChoices, normalChoices)
                  : normalChoices

                if (this._isSearching) {
                  choiceLimit = searchResultLimit
                } else if (renderChoiceLimit && renderChoiceLimit > 0 && !withinGroup) {
                  choiceLimit = renderChoiceLimit
                } // Add each choice to dropdown within range

                for (let i = 0; i < choiceLimit; i += 1) {
                  if (sortedChoices[i]) {
                    appendChoice(sortedChoices[i])
                  }
                }

                return fragment
              }

              _proto._createItemsFragment = function _createItemsFragment (items, fragment) {
                let _this15 = this

                if (fragment === void 0) {
                  fragment = document.createDocumentFragment()
                }

                // Create fragment to add elements to
                let _this$config2 = this.config
                let shouldSortItems = _this$config2.shouldSortItems
                let sorter = _this$config2.sorter
                let removeItemButton = _this$config2.removeItemButton // If sorting is enabled, filter items

                if (shouldSortItems && !this._isSelectOneElement) {
                  items.sort(sorter)
                }

                if (this._isTextElement) {
                  // Update the value of the hidden input
                  this.passedElement.value = items
                } else {
                  // Update the options of the hidden input
                  this.passedElement.options = items
                }

                let addItemToFragment = function addItemToFragment (item) {
                  // Create new list element
                  let listItem = _this15._getTemplate('item', item, removeItemButton) // Append it to list

                  fragment.appendChild(listItem)
                } // Add each list item to list

                items.forEach(addItemToFragment)
                return fragment
              }

              _proto._triggerChange = function _triggerChange (value) {
                if (value === undefined || value === null) {
                  return
                }

                this.passedElement.triggerEvent(EVENTS.change, {
                  value
                })
              }

              _proto._selectPlaceholderChoice = function _selectPlaceholderChoice () {
                let placeholderChoice = this._store.placeholderChoice

                if (placeholderChoice) {
                  this._addItem({
                    value: placeholderChoice.value,
                    label: placeholderChoice.label,
                    choiceId: placeholderChoice.id,
                    groupId: placeholderChoice.groupId,
                    placeholder: placeholderChoice.placeholder
                  })

                  this._triggerChange(placeholderChoice.value)
                }
              }

              _proto._handleButtonAction = function _handleButtonAction (activeItems, element) {
                if (!activeItems || !element || !this.config.removeItems || !this.config.removeItemButton) {
                  return
                }

                let itemId = element.parentNode.getAttribute('data-id')
                let itemToRemove = activeItems.find((item) => {
                  return item.id === parseInt(itemId, 10)
                }) // Remove item associated with button

                this._removeItem(itemToRemove)

                this._triggerChange(itemToRemove.value)

                if (this._isSelectOneElement) {
                  this._selectPlaceholderChoice()
                }
              }

              _proto._handleItemAction = function _handleItemAction (activeItems, element, hasShiftKey) {
                let _this16 = this

                if (hasShiftKey === void 0) {
                  hasShiftKey = false
                }

                if (!activeItems || !element || !this.config.removeItems || this._isSelectOneElement) {
                  return
                }

                let passedId = element.getAttribute('data-id') // We only want to select one item with a click
                // so we deselect any items that aren't the target
                // unless shift is being pressed

                activeItems.forEach((item) => {
                  if (item.id === parseInt(passedId, 10) && !item.highlighted) {
                    _this16.highlightItem(item)
                  } else if (!hasShiftKey && item.highlighted) {
                    _this16.unhighlightItem(item)
                  }
                }) // Focus input as without focus, a user cannot do anything with a
                // highlighted item

                this.input.focus()
              }

              _proto._handleChoiceAction = function _handleChoiceAction (activeItems, element) {
                if (!activeItems || !element) {
                  return
                } // If we are clicking on an option

                let id = element.dataset.id

                let choice = this._store.getChoiceById(id)

                if (!choice) {
                  return
                }

                let passedKeyCode = activeItems[0] && activeItems[0].keyCode ? activeItems[0].keyCode : null
                let hasActiveDropdown = this.dropdown.isActive // Update choice keyCode

                choice.keyCode = passedKeyCode
                this.passedElement.triggerEvent(EVENTS.choice, {
                  choice
                })

                if (!choice.selected && !choice.disabled) {
                  let canAddItem = this._canAddItem(activeItems, choice.value)

                  if (canAddItem.response) {
                    this._addItem({
                      value: choice.value,
                      label: choice.label,
                      choiceId: choice.id,
                      groupId: choice.groupId,
                      customProperties: choice.customProperties,
                      placeholder: choice.placeholder,
                      keyCode: choice.keyCode
                    })

                    this._triggerChange(choice.value)
                  }
                }

                this.clearInput() // We want to close the dropdown if we are dealing with a single select box

                if (hasActiveDropdown && this._isSelectOneElement) {
                  this.hideDropdown(true)
                  this.containerOuter.focus()
                }
              }

              _proto._handleBackspace = function _handleBackspace (activeItems) {
                if (!this.config.removeItems || !activeItems) {
                  return
                }

                let lastItem = activeItems[activeItems.length - 1]
                let hasHighlightedItems = activeItems.some((item) => {
                  return item.highlighted
                }) // If editing the last item is allowed and there are not other selected items,
                // we can edit the item value. Otherwise if we can remove items, remove all selected items

                if (this.config.editItems && !hasHighlightedItems && lastItem) {
                  this.input.value = lastItem.value
                  this.input.setWidth()

                  this._removeItem(lastItem)

                  this._triggerChange(lastItem.value)
                } else {
                  if (!hasHighlightedItems) {
                    // Highlight last item if none already highlighted
                    this.highlightItem(lastItem, false)
                  }

                  this.removeHighlightedItems(true)
                }
              }

              _proto._startLoading = function _startLoading () {
                this._store.dispatch(setIsLoading(true))
              }

              _proto._stopLoading = function _stopLoading () {
                this._store.dispatch(setIsLoading(false))
              }

              _proto._handleLoadingState = function _handleLoadingState (setLoading) {
                if (setLoading === void 0) {
                  setLoading = true
                }

                let placeholderItem = this.itemList.getChild(`.${  this.config.classNames.placeholder}`)

                if (setLoading) {
                  this.disable()
                  this.containerOuter.addLoadingState()

                  if (this._isSelectOneElement) {
                    if (!placeholderItem) {
                      placeholderItem = this._getTemplate('placeholder', this.config.loadingText)
                      this.itemList.append(placeholderItem)
                    } else {
                      placeholderItem.innerHTML = this.config.loadingText
                    }
                  } else {
                    this.input.placeholder = this.config.loadingText
                  }
                } else {
                  this.enable()
                  this.containerOuter.removeLoadingState()

                  if (this._isSelectOneElement) {
                    placeholderItem.innerHTML = this._placeholderValue || ''
                  } else {
                    this.input.placeholder = this._placeholderValue || ''
                  }
                }
              }

              _proto._handleSearch = function _handleSearch (value) {
                if (!value || !this.input.isFocussed) {
                  return
                }

                let choices = this._store.choices
                let _this$config3 = this.config
                let searchFloor = _this$config3.searchFloor
                let searchChoices = _this$config3.searchChoices
                let hasUnactiveChoices = choices.some((option) => {
                  return !option.active
                }) // Check that we have a value to search and the input was an alphanumeric character

                if (value && value.length >= searchFloor) {
                  let resultCount = searchChoices ? this._searchChoices(value) : 0 // Trigger search event

                  this.passedElement.triggerEvent(EVENTS.search, {
                    value,
                    resultCount
                  })
                } else if (hasUnactiveChoices) {
                  // Otherwise reset choices to active
                  this._isSearching = false

                  this._store.dispatch(choices_activateChoices(true))
                }
              }

              _proto._canAddItem = function _canAddItem (activeItems, value) {
                let canAddItem = true
                let notice =
                  typeof this.config.addItemText === 'function'
                    ? this.config.addItemText(value)
                    : this.config.addItemText

                if (!this._isSelectOneElement) {
                  let isDuplicateValue = existsInArray(activeItems, value)

                  if (this.config.maxItemCount > 0 && this.config.maxItemCount <= activeItems.length) {
                    // If there is a max entry limit and we have reached that limit
                    // don't update
                    canAddItem = false
                    notice =
                      typeof this.config.maxItemText === 'function'
                        ? this.config.maxItemText(this.config.maxItemCount)
                        : this.config.maxItemText
                  }

                  if (!this.config.duplicateItemsAllowed && isDuplicateValue && canAddItem) {
                    canAddItem = false
                    notice =
                      typeof this.config.uniqueItemText === 'function'
                        ? this.config.uniqueItemText(value)
                        : this.config.uniqueItemText
                  }

                  if (
                    this._isTextElement &&
                    this.config.addItems &&
                    canAddItem &&
                    typeof this.config.addItemFilter === 'function' &&
                    !this.config.addItemFilter(value)
                  ) {
                    canAddItem = false
                    notice =
                      typeof this.config.customAddItemText === 'function'
                        ? this.config.customAddItemText(value)
                        : this.config.customAddItemText
                  }
                }

                return {
                  response: canAddItem,
                  notice
                }
              }

              _proto._searchChoices = function _searchChoices (value) {
                let newValue = typeof value === 'string' ? value.trim() : value
                let currentValue =
                  typeof this._currentValue === 'string' ? this._currentValue.trim() : this._currentValue

                if (newValue.length < 1 && newValue === `${currentValue  } `) {
                  return 0
                } // If new value matches the desired length and is not the same as the current value with a space

                let haystack = this._store.searchableChoices
                let needle = newValue
                let keys = [].concat(this.config.searchFields)
                let options = Object.assign(this.config.fuseOptions, {
                  keys
                })
                let fuse = new fuse_default.a(haystack, options)
                let results = fuse.search(needle)
                this._currentValue = newValue
                this._highlightPosition = 0
                this._isSearching = true

                this._store.dispatch(choices_filterChoices(results))

                return results.length
              }

              _proto._addEventListeners = function _addEventListeners () {
                let _document = document
                let documentElement = _document.documentElement // capture events - can cancel event processing or propagation

                documentElement.addEventListener('touchend', this._onTouchEnd, true)
                this.containerOuter.element.addEventListener('keydown', this._onKeyDown, true)
                this.containerOuter.element.addEventListener('mousedown', this._onMouseDown, true) // passive events - doesn't call `preventDefault` or `stopPropagation`

                documentElement.addEventListener('click', this._onClick, {
                  passive: true
                })
                documentElement.addEventListener('touchmove', this._onTouchMove, {
                  passive: true
                })
                this.dropdown.element.addEventListener('mouseover', this._onMouseOver, {
                  passive: true
                })

                if (this._isSelectOneElement) {
                  this.containerOuter.element.addEventListener('focus', this._onFocus, {
                    passive: true
                  })
                  this.containerOuter.element.addEventListener('blur', this._onBlur, {
                    passive: true
                  })
                }

                this.input.element.addEventListener('keyup', this._onKeyUp, {
                  passive: true
                })
                this.input.element.addEventListener('focus', this._onFocus, {
                  passive: true
                })
                this.input.element.addEventListener('blur', this._onBlur, {
                  passive: true
                })

                if (this.input.element.form) {
                  this.input.element.form.addEventListener('reset', this._onFormReset, {
                    passive: true
                  })
                }

                this.input.addEventListeners()
              }

              _proto._removeEventListeners = function _removeEventListeners () {
                let _document2 = document
                let documentElement = _document2.documentElement
                documentElement.removeEventListener('touchend', this._onTouchEnd, true)
                this.containerOuter.element.removeEventListener('keydown', this._onKeyDown, true)
                this.containerOuter.element.removeEventListener('mousedown', this._onMouseDown, true)
                documentElement.removeEventListener('click', this._onClick)
                documentElement.removeEventListener('touchmove', this._onTouchMove)
                this.dropdown.element.removeEventListener('mouseover', this._onMouseOver)

                if (this._isSelectOneElement) {
                  this.containerOuter.element.removeEventListener('focus', this._onFocus)
                  this.containerOuter.element.removeEventListener('blur', this._onBlur)
                }

                this.input.element.removeEventListener('keyup', this._onKeyUp)
                this.input.element.removeEventListener('focus', this._onFocus)
                this.input.element.removeEventListener('blur', this._onBlur)

                if (this.input.element.form) {
                  this.input.element.form.removeEventListener('reset', this._onFormReset)
                }

                this.input.removeEventListeners()
              }
              /**
               * @param {KeyboardEvent} event
               */

              _proto._onKeyDown = function _onKeyDown (event) {
                let _keyDownActions

                let target = event.target
                let keyCode = event.keyCode
                let ctrlKey = event.ctrlKey
                let metaKey = event.metaKey
                let activeItems = this._store.activeItems
                let hasFocusedInput = this.input.isFocussed
                let hasActiveDropdown = this.dropdown.isActive
                let hasItems = this.itemList.hasChildren()
                let keyString = String.fromCharCode(keyCode)
                let BACK_KEY = KEY_CODES.BACK_KEY
                let DELETE_KEY = KEY_CODES.DELETE_KEY
                let ENTER_KEY = KEY_CODES.ENTER_KEY
                let A_KEY = KEY_CODES.A_KEY
                let ESC_KEY = KEY_CODES.ESC_KEY
                let UP_KEY = KEY_CODES.UP_KEY
                let DOWN_KEY = KEY_CODES.DOWN_KEY
                let PAGE_UP_KEY = KEY_CODES.PAGE_UP_KEY
                let PAGE_DOWN_KEY = KEY_CODES.PAGE_DOWN_KEY
                let hasCtrlDownKeyPressed = ctrlKey || metaKey // If a user is typing and the dropdown is not active

                if (!this._isTextElement && /[a-zA-Z0-9-_ ]/.test(keyString)) {
                  this.showDropdown()
                } // Map keys to key actions

                let keyDownActions =
                  ((_keyDownActions = {}),
                  (_keyDownActions[A_KEY] = this._onAKey),
                  (_keyDownActions[ENTER_KEY] = this._onEnterKey),
                  (_keyDownActions[ESC_KEY] = this._onEscapeKey),
                  (_keyDownActions[UP_KEY] = this._onDirectionKey),
                  (_keyDownActions[PAGE_UP_KEY] = this._onDirectionKey),
                  (_keyDownActions[DOWN_KEY] = this._onDirectionKey),
                  (_keyDownActions[PAGE_DOWN_KEY] = this._onDirectionKey),
                  (_keyDownActions[DELETE_KEY] = this._onDeleteKey),
                  (_keyDownActions[BACK_KEY] = this._onDeleteKey),
                  _keyDownActions) // If keycode has a function, run it

                if (keyDownActions[keyCode]) {
                  keyDownActions[keyCode]({
                    event,
                    target,
                    keyCode,
                    metaKey,
                    activeItems,
                    hasFocusedInput,
                    hasActiveDropdown,
                    hasItems,
                    hasCtrlDownKeyPressed
                  })
                }
              }

              _proto._onKeyUp = function _onKeyUp (_ref2) {
                let target = _ref2.target
                let keyCode = _ref2.keyCode
                let value = this.input.value
                let activeItems = this._store.activeItems

                let canAddItem = this._canAddItem(activeItems, value)

                let backKey = KEY_CODES.BACK_KEY
                let deleteKey = KEY_CODES.DELETE_KEY // We are typing into a text input and have a value, we want to show a dropdown
                // notice. Otherwise hide the dropdown

                if (this._isTextElement) {
                  let canShowDropdownNotice = canAddItem.notice && value

                  if (canShowDropdownNotice) {
                    let dropdownItem = this._getTemplate('notice', canAddItem.notice)

                    this.dropdown.element.innerHTML = dropdownItem.outerHTML
                    this.showDropdown(true)
                  } else {
                    this.hideDropdown(true)
                  }
                } else {
                  let userHasRemovedValue = (keyCode === backKey || keyCode === deleteKey) && !target.value
                  let canReactivateChoices = !this._isTextElement && this._isSearching
                  let canSearch = this._canSearch && canAddItem.response

                  if (userHasRemovedValue && canReactivateChoices) {
                    this._isSearching = false

                    this._store.dispatch(choices_activateChoices(true))
                  } else if (canSearch) {
                    this._handleSearch(this.input.value)
                  }
                }

                this._canSearch = this.config.searchEnabled
              }

              _proto._onAKey = function _onAKey (_ref3) {
                let hasItems = _ref3.hasItems
                let hasCtrlDownKeyPressed = _ref3.hasCtrlDownKeyPressed

                // If CTRL + A or CMD + A have been pressed and there are items to select
                if (hasCtrlDownKeyPressed && hasItems) {
                  this._canSearch = false
                  let shouldHightlightAll =
                    this.config.removeItems && !this.input.value && this.input.element === document.activeElement

                  if (shouldHightlightAll) {
                    this.highlightAll()
                  }
                }
              }

              _proto._onEnterKey = function _onEnterKey (_ref4) {
                let event = _ref4.event
                let target = _ref4.target
                let activeItems = _ref4.activeItems
                let hasActiveDropdown = _ref4.hasActiveDropdown
                let enterKey = KEY_CODES.ENTER_KEY
                let targetWasButton = target.hasAttribute('data-button')

                if (this._isTextElement && target.value) {
                  let value = this.input.value

                  let canAddItem = this._canAddItem(activeItems, value)

                  if (canAddItem.response) {
                    this.hideDropdown(true)

                    this._addItem({
                      value
                    })

                    this._triggerChange(value)

                    this.clearInput()
                  }
                }

                if (targetWasButton) {
                  this._handleButtonAction(activeItems, target)

                  event.preventDefault()
                }

                if (hasActiveDropdown) {
                  let highlightedChoice = this.dropdown.getChild(`.${  this.config.classNames.highlightedState}`)

                  if (highlightedChoice) {
                    // add enter keyCode value
                    if (activeItems[0]) {
                      activeItems[0].keyCode = enterKey // eslint-disable-line no-param-reassign
                    }

                    this._handleChoiceAction(activeItems, highlightedChoice)
                  }

                  event.preventDefault()
                } else if (this._isSelectOneElement) {
                  this.showDropdown()
                  event.preventDefault()
                }
              }

              _proto._onEscapeKey = function _onEscapeKey (_ref5) {
                let hasActiveDropdown = _ref5.hasActiveDropdown

                if (hasActiveDropdown) {
                  this.hideDropdown(true)
                  this.containerOuter.focus()
                }
              }

              _proto._onDirectionKey = function _onDirectionKey (_ref6) {
                let event = _ref6.event
                let hasActiveDropdown = _ref6.hasActiveDropdown
                let keyCode = _ref6.keyCode
                let metaKey = _ref6.metaKey
                let downKey = KEY_CODES.DOWN_KEY
                let pageUpKey = KEY_CODES.PAGE_UP_KEY
                let pageDownKey = KEY_CODES.PAGE_DOWN_KEY // If up or down key is pressed, traverse through options

                if (hasActiveDropdown || this._isSelectOneElement) {
                  this.showDropdown()
                  this._canSearch = false
                  let directionInt = keyCode === downKey || keyCode === pageDownKey ? 1 : -1
                  let skipKey = metaKey || keyCode === pageDownKey || keyCode === pageUpKey
                  let selectableChoiceIdentifier = '[data-choice-selectable]'
                  let nextEl

                  if (skipKey) {
                    if (directionInt > 0) {
                      nextEl = this.dropdown.element.querySelector(`${selectableChoiceIdentifier  }:last-of-type`)
                    } else {
                      nextEl = this.dropdown.element.querySelector(selectableChoiceIdentifier)
                    }
                  } else {
                    let currentEl = this.dropdown.element.querySelector(`.${  this.config.classNames.highlightedState}`)

                    if (currentEl) {
                      nextEl = getAdjacentEl(currentEl, selectableChoiceIdentifier, directionInt)
                    } else {
                      nextEl = this.dropdown.element.querySelector(selectableChoiceIdentifier)
                    }
                  }

                  if (nextEl) {
                    // We prevent default to stop the cursor moving
                    // when pressing the arrow
                    if (!isScrolledIntoView(nextEl, this.choiceList.element, directionInt)) {
                      this.choiceList.scrollToChildElement(nextEl, directionInt)
                    }

                    this._highlightChoice(nextEl)
                  } // Prevent default to maintain cursor position whilst
                  // traversing dropdown options

                  event.preventDefault()
                }
              }

              _proto._onDeleteKey = function _onDeleteKey (_ref7) {
                let event = _ref7.event
                let target = _ref7.target
                let hasFocusedInput = _ref7.hasFocusedInput
                let activeItems = _ref7.activeItems

                // If backspace or delete key is pressed and the input has no value
                if (hasFocusedInput && !target.value && !this._isSelectOneElement) {
                  this._handleBackspace(activeItems)

                  event.preventDefault()
                }
              }

              _proto._onTouchMove = function _onTouchMove () {
                if (this._wasTap) {
                  this._wasTap = false
                }
              }

              _proto._onTouchEnd = function _onTouchEnd (event) {
                let _ref8 = event || event.touches[0]
                let target = _ref8.target

                let touchWasWithinContainer = this._wasTap && this.containerOuter.element.contains(target)

                if (touchWasWithinContainer) {
                  let containerWasExactTarget =
                    target === this.containerOuter.element || target === this.containerInner.element

                  if (containerWasExactTarget) {
                    if (this._isTextElement) {
                      this.input.focus()
                    } else if (this._isSelectMultipleElement) {
                      this.showDropdown()
                    }
                  } // Prevents focus event firing

                  event.stopPropagation()
                }

                this._wasTap = true
              }
              /**
               * Handles mousedown event in capture mode for containetOuter.element
               * @param {MouseEvent} event
               */

              _proto._onMouseDown = function _onMouseDown (event) {
                let target = event.target

                if (!(target instanceof HTMLElement)) {
                  return
                } // If we have our mouse down on the scrollbar and are on IE11...

                if (IS_IE11 && this.choiceList.element.contains(target)) {
                  // check if click was on a scrollbar area
                  let firstChoice =
                    /** @type {HTMLElement} */
                    this.choiceList.element.firstElementChild
                  let isOnScrollbar =
                    this._direction === 'ltr'
                      ? event.offsetX >= firstChoice.offsetWidth
                      : event.offsetX < firstChoice.offsetLeft
                  this._isScrollingOnIe = isOnScrollbar
                }

                if (target === this.input.element) {
                  return
                }

                let item = target.closest('[data-button],[data-item],[data-choice]')

                if (item instanceof HTMLElement) {
                  let hasShiftKey = event.shiftKey
                  let activeItems = this._store.activeItems
                  let dataset = item.dataset

                  if ('button' in dataset) {
                    this._handleButtonAction(activeItems, item)
                  } else if ('item' in dataset) {
                    this._handleItemAction(activeItems, item, hasShiftKey)
                  } else if ('choice' in dataset) {
                    this._handleChoiceAction(activeItems, item)
                  }
                }

                event.preventDefault()
              }
              /**
               * Handles mouseover event over this.dropdown
               * @param {MouseEvent} event
               */

              _proto._onMouseOver = function _onMouseOver (_ref9) {
                let target = _ref9.target

                if (target instanceof HTMLElement && 'choice' in target.dataset) {
                  this._highlightChoice(target)
                }
              }

              _proto._onClick = function _onClick (_ref10) {
                let target = _ref10.target
                let clickWasWithinContainer = this.containerOuter.element.contains(target)

                if (clickWasWithinContainer) {
                  if (!this.dropdown.isActive && !this.containerOuter.isDisabled) {
                    if (this._isTextElement) {
                      if (document.activeElement !== this.input.element) {
                        this.input.focus()
                      }
                    } else {
                      this.showDropdown()
                      this.containerOuter.focus()
                    }
                  } else if (
                    this._isSelectOneElement &&
                    target !== this.input.element &&
                    !this.dropdown.element.contains(target)
                  ) {
                    this.hideDropdown()
                  }
                } else {
                  let hasHighlightedItems = this._store.highlightedActiveItems.length > 0

                  if (hasHighlightedItems) {
                    this.unhighlightAll()
                  }

                  this.containerOuter.removeFocusState()
                  this.hideDropdown(true)
                }
              }

              _proto._onFocus = function _onFocus (_ref11) {
                let _this17 = this
                let _focusActions

                let target = _ref11.target
                let focusWasWithinContainer = this.containerOuter.element.contains(target)

                if (!focusWasWithinContainer) {
                  return
                }

                let focusActions =
                  ((_focusActions = {}),
                  (_focusActions[TEXT_TYPE] = function () {
                    if (target === _this17.input.element) {
                      _this17.containerOuter.addFocusState()
                    }
                  }),
                  (_focusActions[SELECT_ONE_TYPE] = function () {
                    _this17.containerOuter.addFocusState()

                    if (target === _this17.input.element) {
                      _this17.showDropdown(true)
                    }
                  }),
                  (_focusActions[SELECT_MULTIPLE_TYPE] = function () {
                    if (target === _this17.input.element) {
                      _this17.showDropdown(true) // If element is a select box, the focused element is the container and the dropdown
                      // isn't already open, focus and show dropdown

                      _this17.containerOuter.addFocusState()
                    }
                  }),
                  _focusActions)
                focusActions[this.passedElement.element.type]()
              }

              _proto._onBlur = function _onBlur (_ref12) {
                let _this18 = this

                let target = _ref12.target
                let blurWasWithinContainer = this.containerOuter.element.contains(target)

                if (blurWasWithinContainer && !this._isScrollingOnIe) {
                  let _blurActions

                  let activeItems = this._store.activeItems
                  let hasHighlightedItems = activeItems.some((item) => {
                    return item.highlighted
                  })
                  let blurActions =
                    ((_blurActions = {}),
                    (_blurActions[TEXT_TYPE] = function () {
                      if (target === _this18.input.element) {
                        _this18.containerOuter.removeFocusState()

                        if (hasHighlightedItems) {
                          _this18.unhighlightAll()
                        }

                        _this18.hideDropdown(true)
                      }
                    }),
                    (_blurActions[SELECT_ONE_TYPE] = function () {
                      _this18.containerOuter.removeFocusState()

                      if (
                        target === _this18.input.element ||
                        (target === _this18.containerOuter.element && !_this18._canSearch)
                      ) {
                        _this18.hideDropdown(true)
                      }
                    }),
                    (_blurActions[SELECT_MULTIPLE_TYPE] = function () {
                      if (target === _this18.input.element) {
                        _this18.containerOuter.removeFocusState()

                        _this18.hideDropdown(true)

                        if (hasHighlightedItems) {
                          _this18.unhighlightAll()
                        }
                      }
                    }),
                    _blurActions)
                  blurActions[this.passedElement.element.type]()
                } else {
                  // On IE11, clicking the scollbar blurs our input and thus
                  // closes the dropdown. To stop this, we refocus our input
                  // if we know we are on IE *and* are scrolling.
                  this._isScrollingOnIe = false
                  this.input.element.focus()
                }
              }

              _proto._onFormReset = function _onFormReset () {
                this._store.dispatch(resetTo(this._initialState))
              }

              _proto._highlightChoice = function _highlightChoice (el) {
                let _this19 = this

                if (el === void 0) {
                  el = null
                }

                let choices = Array.from(this.dropdown.element.querySelectorAll('[data-choice-selectable]'))

                if (!choices.length) {
                  return
                }

                let passedEl = el
                let highlightedChoices = Array.from(
                  this.dropdown.element.querySelectorAll(`.${  this.config.classNames.highlightedState}`)
                ) // Remove any highlighted choices

                highlightedChoices.forEach((choice) => {
                  choice.classList.remove(_this19.config.classNames.highlightedState)
                  choice.setAttribute('aria-selected', 'false')
                })

                if (passedEl) {
                  this._highlightPosition = choices.indexOf(passedEl)
                } else {
                  // Highlight choice based on last known highlight location
                  if (choices.length > this._highlightPosition) {
                    // If we have an option to highlight
                    passedEl = choices[this._highlightPosition]
                  } else {
                    // Otherwise highlight the option before
                    passedEl = choices[choices.length - 1]
                  }

                  if (!passedEl) {
                    passedEl = choices[0]
                  }
                }

                passedEl.classList.add(this.config.classNames.highlightedState)
                passedEl.setAttribute('aria-selected', 'true')
                this.passedElement.triggerEvent(EVENTS.highlightChoice, {
                  el: passedEl
                })

                if (this.dropdown.isActive) {
                  // IE11 ignores aria-label and blocks virtual keyboard
                  // if aria-activedescendant is set without a dropdown
                  this.input.setActiveDescendant(passedEl.id)
                  this.containerOuter.setActiveDescendant(passedEl.id)
                }
              }

              _proto._addItem = function _addItem (_ref13) {
                let value = _ref13.value
                let _ref13$label = _ref13.label
                let label = _ref13$label === void 0 ? null : _ref13$label
                let _ref13$choiceId = _ref13.choiceId
                let choiceId = _ref13$choiceId === void 0 ? -1 : _ref13$choiceId
                let _ref13$groupId = _ref13.groupId
                let groupId = _ref13$groupId === void 0 ? -1 : _ref13$groupId
                let _ref13$customProperti = _ref13.customProperties
                let customProperties = _ref13$customProperti === void 0 ? null : _ref13$customProperti
                let _ref13$placeholder = _ref13.placeholder
                let placeholder = _ref13$placeholder === void 0 ? false : _ref13$placeholder
                let _ref13$keyCode = _ref13.keyCode
                let keyCode = _ref13$keyCode === void 0 ? null : _ref13$keyCode
                let passedValue = typeof value === 'string' ? value.trim() : value
                let passedKeyCode = keyCode
                let passedCustomProperties = customProperties
                let items = this._store.items
                let passedLabel = label || passedValue
                let passedOptionId = choiceId || -1
                let group = groupId >= 0 ? this._store.getGroupById(groupId) : null
                let id = items ? items.length + 1 : 1 // If a prepended value has been passed, prepend it

                if (this.config.prependValue) {
                  passedValue = this.config.prependValue + passedValue.toString()
                } // If an appended value has been passed, append it

                if (this.config.appendValue) {
                  passedValue += this.config.appendValue.toString()
                }

                this._store.dispatch(
                  items_addItem({
                    value: passedValue,
                    label: passedLabel,
                    id,
                    choiceId: passedOptionId,
                    groupId,
                    customProperties,
                    placeholder,
                    keyCode: passedKeyCode
                  })
                )

                if (this._isSelectOneElement) {
                  this.removeActiveItems(id)
                } // Trigger change event

                this.passedElement.triggerEvent(EVENTS.addItem, {
                  id,
                  value: passedValue,
                  label: passedLabel,
                  customProperties: passedCustomProperties,
                  groupValue: group && group.value ? group.value : undefined,
                  keyCode: passedKeyCode
                })
                return this
              }

              _proto._removeItem = function _removeItem (item) {
                if (!item || !isType('Object', item)) {
                  return this
                }

                let id = item.id
                let value = item.value
                let label = item.label
                let choiceId = item.choiceId
                let groupId = item.groupId
                let group = groupId >= 0 ? this._store.getGroupById(groupId) : null

                this._store.dispatch(items_removeItem(id, choiceId))

                if (group && group.value) {
                  this.passedElement.triggerEvent(EVENTS.removeItem, {
                    id,
                    value,
                    label,
                    groupValue: group.value
                  })
                } else {
                  this.passedElement.triggerEvent(EVENTS.removeItem, {
                    id,
                    value,
                    label
                  })
                }

                return this
              }

              _proto._addChoice = function _addChoice (_ref14) {
                let value = _ref14.value
                let _ref14$label = _ref14.label
                let label = _ref14$label === void 0 ? null : _ref14$label
                let _ref14$isSelected = _ref14.isSelected
                let isSelected = _ref14$isSelected === void 0 ? false : _ref14$isSelected
                let _ref14$isDisabled = _ref14.isDisabled
                let isDisabled = _ref14$isDisabled === void 0 ? false : _ref14$isDisabled
                let _ref14$groupId = _ref14.groupId
                let groupId = _ref14$groupId === void 0 ? -1 : _ref14$groupId
                let _ref14$customProperti = _ref14.customProperties
                let customProperties = _ref14$customProperti === void 0 ? null : _ref14$customProperti
                let _ref14$placeholder = _ref14.placeholder
                let placeholder = _ref14$placeholder === void 0 ? false : _ref14$placeholder
                let _ref14$keyCode = _ref14.keyCode
                let keyCode = _ref14$keyCode === void 0 ? null : _ref14$keyCode

                if (typeof value === 'undefined' || value === null) {
                  return
                } // Generate unique id

                let choices = this._store.choices
                let choiceLabel = label || value
                let choiceId = choices ? choices.length + 1 : 1
                let choiceElementId = `${this._baseId  }-${  this._idNames.itemChoice  }-${  choiceId}`

                this._store.dispatch(
                  choices_addChoice({
                    id: choiceId,
                    groupId,
                    elementId: choiceElementId,
                    value,
                    label: choiceLabel,
                    disabled: isDisabled,
                    customProperties,
                    placeholder,
                    keyCode
                  })
                )

                if (isSelected) {
                  this._addItem({
                    value,
                    label: choiceLabel,
                    choiceId,
                    customProperties,
                    placeholder,
                    keyCode
                  })
                }
              }

              _proto._addGroup = function _addGroup (_ref15) {
                let _this20 = this

                let group = _ref15.group
                let id = _ref15.id
                let _ref15$valueKey = _ref15.valueKey
                let valueKey = _ref15$valueKey === void 0 ? 'value' : _ref15$valueKey
                let _ref15$labelKey = _ref15.labelKey
                let labelKey = _ref15$labelKey === void 0 ? 'label' : _ref15$labelKey
                let groupChoices = isType('Object', group)
                  ? group.choices
                  : Array.from(group.getElementsByTagName('OPTION'))
                let groupId = id || Math.floor(new Date().valueOf() * Math.random())
                let isDisabled = group.disabled ? group.disabled : false

                if (groupChoices) {
                  this._store.dispatch(
                    groups_addGroup({
                      value: group.label,
                      id: groupId,
                      active: true,
                      disabled: isDisabled
                    })
                  )

                  let addGroupChoices = function addGroupChoices (choice) {
                    let isOptDisabled = choice.disabled || (choice.parentNode && choice.parentNode.disabled)

                    _this20._addChoice({
                      value: choice[valueKey],
                      label: isType('Object', choice) ? choice[labelKey] : choice.innerHTML,
                      isSelected: choice.selected,
                      isDisabled: isOptDisabled,
                      groupId,
                      customProperties: choice.customProperties,
                      placeholder: choice.placeholder
                    })
                  }

                  groupChoices.forEach(addGroupChoices)
                } else {
                  this._store.dispatch(
                    groups_addGroup({
                      value: group.label,
                      id: group.id,
                      active: false,
                      disabled: group.disabled
                    })
                  )
                }
              }

              _proto._getTemplate = function _getTemplate (template) {
                let _this$_templates$temp

                if (!template) {
                  return null
                }

                let classNames = this.config.classNames

                for (
                  var _len = arguments.length, args = new Array(_len > 1 ? _len - 1 : 0), _key = 1;
                  _key < _len;
                  _key++
                ) {
                  args[_key - 1] = arguments[_key]
                }

                return (_this$_templates$temp = this._templates[template]).call.apply(
                  _this$_templates$temp,
                  [this, classNames].concat(args)
                )
              }

              _proto._createTemplates = function _createTemplates () {
                let callbackOnCreateTemplates = this.config.callbackOnCreateTemplates
                let userTemplates = {}

                if (callbackOnCreateTemplates && typeof callbackOnCreateTemplates === 'function') {
                  userTemplates = callbackOnCreateTemplates.call(this, strToEl)
                }

                this._templates = cjs_default()(TEMPLATES, userTemplates)
              }

              _proto._createElements = function _createElements () {
                this.containerOuter = new container_Container({
                  element: this._getTemplate(
                    'containerOuter',
                    this._direction,
                    this._isSelectElement,
                    this._isSelectOneElement,
                    this.config.searchEnabled,
                    this.passedElement.element.type
                  ),
                  classNames: this.config.classNames,
                  type: this.passedElement.element.type,
                  position: this.config.position
                })
                this.containerInner = new container_Container({
                  element: this._getTemplate('containerInner'),
                  classNames: this.config.classNames,
                  type: this.passedElement.element.type,
                  position: this.config.position
                })
                this.input = new input_Input({
                  element: this._getTemplate('input', this._placeholderValue),
                  classNames: this.config.classNames,
                  type: this.passedElement.element.type,
                  preventPaste: !this.config.paste
                })
                this.choiceList = new list_List({
                  element: this._getTemplate('choiceList', this._isSelectOneElement)
                })
                this.itemList = new list_List({
                  element: this._getTemplate('itemList', this._isSelectOneElement)
                })
                this.dropdown = new Dropdown({
                  element: this._getTemplate('dropdown'),
                  classNames: this.config.classNames,
                  type: this.passedElement.element.type
                })
              }

              _proto._createStructure = function _createStructure () {
                // Hide original element
                this.passedElement.conceal() // Wrap input in container preserving DOM ordering

                this.containerInner.wrap(this.passedElement.element) // Wrapper inner container with outer container

                this.containerOuter.wrap(this.containerInner.element)

                if (this._isSelectOneElement) {
                  this.input.placeholder = this.config.searchPlaceholderValue || ''
                } else if (this._placeholderValue) {
                  this.input.placeholder = this._placeholderValue
                  this.input.setWidth()
                }

                this.containerOuter.element.appendChild(this.containerInner.element)
                this.containerOuter.element.appendChild(this.dropdown.element)
                this.containerInner.element.appendChild(this.itemList.element)

                if (!this._isTextElement) {
                  this.dropdown.element.appendChild(this.choiceList.element)
                }

                if (!this._isSelectOneElement) {
                  this.containerInner.element.appendChild(this.input.element)
                } else if (this.config.searchEnabled) {
                  this.dropdown.element.insertBefore(this.input.element, this.dropdown.element.firstChild)
                }

                if (this._isSelectElement) {
                  this._highlightPosition = 0
                  this._isSearching = false

                  this._startLoading()

                  if (this._presetGroups.length) {
                    this._addPredefinedGroups(this._presetGroups)
                  } else {
                    this._addPredefinedChoices(this._presetChoices)
                  }

                  this._stopLoading()
                }

                if (this._isTextElement) {
                  this._addPredefinedItems(this._presetItems)
                }
              }

              _proto._addPredefinedGroups = function _addPredefinedGroups (groups) {
                let _this21 = this

                // If we have a placeholder option
                let placeholderChoice = this.passedElement.placeholderOption

                if (placeholderChoice && placeholderChoice.parentNode.tagName === 'SELECT') {
                  this._addChoice({
                    value: placeholderChoice.value,
                    label: placeholderChoice.innerHTML,
                    isSelected: placeholderChoice.selected,
                    isDisabled: placeholderChoice.disabled,
                    placeholder: true
                  })
                }

                groups.forEach((group) => {
                  return _this21._addGroup({
                    group,
                    id: group.id || null
                  })
                })
              }

              _proto._addPredefinedChoices = function _addPredefinedChoices (choices) {
                let _this22 = this

                // If sorting is enabled or the user is searching, filter choices
                if (this.config.shouldSort) {
                  choices.sort(this.config.sorter)
                }

                let hasSelectedChoice = choices.some((choice) => {
                  return choice.selected
                })
                let firstEnabledChoiceIndex = choices.findIndex((choice) => {
                  return choice.disabled === undefined || !choice.disabled
                })
                choices.forEach((choice, index) => {
                  let value = choice.value
                  let label = choice.label
                  let customProperties = choice.customProperties
                  let placeholder = choice.placeholder

                  if (_this22._isSelectElement) {
                    // If the choice is actually a group
                    if (choice.choices) {
                      _this22._addGroup({
                        group: choice,
                        id: choice.id || null
                      })
                    } else {
                      /**
                       * If there is a selected choice already or the choice is not the first in
                       * the array, add each choice normally.
                       *
                       * Otherwise we pre-select the first enabled choice in the array ("select-one" only)
                       */
                      let shouldPreselect =
                        _this22._isSelectOneElement && !hasSelectedChoice && index === firstEnabledChoiceIndex
                      let isSelected = shouldPreselect ? true : choice.selected
                      let isDisabled = choice.disabled

                      _this22._addChoice({
                        value,
                        label,
                        isSelected,
                        isDisabled,
                        customProperties,
                        placeholder
                      })
                    }
                  } else {
                    _this22._addChoice({
                      value,
                      label,
                      isSelected: choice.selected,
                      isDisabled: choice.disabled,
                      customProperties,
                      placeholder
                    })
                  }
                })
              }
              /**
               * @param {Item[]} items
               */

              _proto._addPredefinedItems = function _addPredefinedItems (items) {
                let _this23 = this

                items.forEach((item) => {
                  if (typeof item === 'object' && item.value) {
                    _this23._addItem({
                      value: item.value,
                      label: item.label,
                      choiceId: item.id,
                      customProperties: item.customProperties,
                      placeholder: item.placeholder
                    })
                  }

                  if (typeof item === 'string') {
                    _this23._addItem({
                      value: item
                    })
                  }
                })
              }

              _proto._setChoiceOrItem = function _setChoiceOrItem (item) {
                let _this24 = this

                let itemType = getType(item).toLowerCase()
                let handleType = {
                  object: function object () {
                    if (!item.value) {
                      return
                    } // If we are dealing with a select input, we need to create an option first
                    // that is then selected. For text inputs we can just add items normally.

                    if (!_this24._isTextElement) {
                      _this24._addChoice({
                        value: item.value,
                        label: item.label,
                        isSelected: true,
                        isDisabled: false,
                        customProperties: item.customProperties,
                        placeholder: item.placeholder
                      })
                    } else {
                      _this24._addItem({
                        value: item.value,
                        label: item.label,
                        choiceId: item.id,
                        customProperties: item.customProperties,
                        placeholder: item.placeholder
                      })
                    }
                  },
                  string: function string () {
                    if (!_this24._isTextElement) {
                      _this24._addChoice({
                        value: item,
                        label: item,
                        isSelected: true,
                        isDisabled: false
                      })
                    } else {
                      _this24._addItem({
                        value: item
                      })
                    }
                  }
                }
                handleType[itemType]()
              }

              _proto._findAndSelectChoiceByValue = function _findAndSelectChoiceByValue (val) {
                let _this25 = this

                let choices = this._store.choices // Check 'value' property exists and the choice isn't already selected

                let foundChoice = choices.find((choice) => {
                  return _this25.config.valueComparer(choice.value, val)
                })

                if (foundChoice && !foundChoice.selected) {
                  this._addItem({
                    value: foundChoice.value,
                    label: foundChoice.label,
                    choiceId: foundChoice.id,
                    groupId: foundChoice.groupId,
                    customProperties: foundChoice.customProperties,
                    placeholder: foundChoice.placeholder,
                    keyCode: foundChoice.keyCode
                  })
                }
              }

              _proto._generatePlaceholderValue = function _generatePlaceholderValue () {
                if (this._isSelectElement) {
                  let placeholderOption = this.passedElement.placeholderOption
                  return placeholderOption ? placeholderOption.text : false
                }

                let _this$config4 = this.config
                let placeholder = _this$config4.placeholder
                let placeholderValue = _this$config4.placeholderValue
                let dataset = this.passedElement.element.dataset

                if (placeholder) {
                  if (placeholderValue) {
                    return placeholderValue
                  }

                  if (dataset.placeholder) {
                    return dataset.placeholder
                  }
                }

                return false
              }

              return Choices
            })()

          /* harmony default export */ let scripts_choices = (__webpack_exports__.default = choices_Choices)
          /***/
        }
        /******/
      ]
    ).default
  })
})

let choices$1 = unwrapExports(choices)

export default choices$1
