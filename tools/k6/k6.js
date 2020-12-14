import http from 'k6/http'
import {sleep, check} from 'k6'
export const options = {
  duration: '20s',
  vus: 1,
  thresholds: {
    http_req_duration: ['p(99)<200']
  }
}

export default function () {
  let res = http.get('http://localhost:8000')
  check(res, {'status was 200': (r) => r.status === 200 })
  sleep(1)
}

/**
 * Vanilla
 **************
 /demo.html avg=32.3ms   min=31.73ms med=32.07ms max=33.71ms  p(90)=33ms     p(95)=33.43ms
 /          avg=81.06ms  min=70.03ms med=76.52ms max=111.04ms p(90)=91.49ms  p(95)=94.56ms
 /tutoriels avg=122.56ms min=107.37ms med=116.61ms max=168.64ms p(90)=141.89ms p(95)=165.91ms
 *
 ***************
 * Docker
 **************
 /demo.html avg=36.49ms  min=33.28ms med=35.08ms  max=54.93ms  p(90)=37.95ms  p(95)=40.78ms
 /          avg=79.21ms  min=68.17ms med=80.29ms max=88.39ms p(90)=85.4ms  p(95)=85.77ms
 /tutoriels avg=123.71ms min=108.83ms med=120.99ms max=156.67ms p(90)=139.85ms p(95)=149.15ms
*/
