export interface Entity {
  id: string;
  model: string;
  properties: object;
  view: string;
  parent_entity_id: string;
  is_active: boolean;
  published_at: string;
  unpublished_at: string;
  version: number;
  contents: Array<object>;
  entities_related: Array<object>;
}
