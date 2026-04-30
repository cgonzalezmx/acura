export interface DataGridColumns {
  [column: number]: {
    header: string,
    data: string
    width?: string
  }
}

export interface DataGridRow {
  [column: string]: number | string
}