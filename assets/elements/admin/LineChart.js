/**
 * Crée un graphique avec des lignes
 *
 * ## Exemple d'utilisation
 *
 * ```
 * <line-charts points="[{l: 'a1', v: 1203},{l: 'a2', v: 1233}]" x="l" y="v"/>
 * ```
 *
 * @property {ShadowRoot} root
 * @property {Chart} chart
 */
export class LineChart extends HTMLElement {
  static get observedAttributes() {
    return ["hidden"];
  }

  constructor() {
    super();
    this.root = this.attachShadow({ mode: "open" });
  }

  async connectedCallback() {
    /** @var {Chart} Chart **/
    const ChartImport = await import("chart.js");
    const Chart = ChartImport.default || ChartImport // Handle difference between vite / rollup
    this.style.display = "block";
    this.root.innerHTML = `<style>
      canvas {
        width: 100% !important;
        height: 375px;
      }
    </style>
    <canvas width="1000" height="375"></canvas>`;
    const points = JSON.parse(this.getAttribute("points"));
    const xKey = this.getAttribute("x") || "x";
    const yKey = this.getAttribute("y") || "y";
    this.chart = new Chart(this.root.querySelector("canvas"), {
      type: "line",
      data: {
        labels: points.map((point) => point[xKey]),
        datasets: [
          {
            cubicInterpolationMode: "monotone",
            data: points.map((point) => point[yKey]),
            backgroundColor: "#4869ee0C",
            borderColor: "#4869ee",
            borderWidth: 2,
          },
        ],
      },
      options: {
        legend: {
          display: false,
        },
        elements: {
          line: {
            tension: 0.3,
          },
        },
        responsive: true,
        maintainAspectRatio: false,
        scales: {
          xAxes: [
            {
              gridLines: {
                drawOnChartArea: false,
              },
            },
          ],
          yAxes: [
            {
              ticks: {
                beginAtZero: true,
              },
            },
          ],
        },
        animation: {
          duration: 0,
        },
        hover: {
          animationDuration: 0,
        },
        responsiveAnimationDuration: 0,
      },
    });
  }

  attributeChangedCallback(name, oldValue, newValue) {
    if (this.chart && name === "hidden" && newValue === null) {
      this.chart.canvas.style.height = "375px";
      this.chart.render();
    }
  }

  disconnectedCallback() {
    this.innerHTML = "";
  }
}
