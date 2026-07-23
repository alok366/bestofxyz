// MOCK DATA — Submit Resource Flow

export const mockCategoriesList = [
  { id: 'cat_programming', name: 'Programming' },
  { id: 'cat_design', name: 'Design Tools' },
  { id: 'cat_datascience', name: 'Data Science & AI' },
  { id: 'cat_devops', name: 'DevOps & Cloud' },
  { id: 'cat_gamedev', name: 'Game Development' }
];

export const mockExistingSubcategories = {
  cat_programming: [
    { id: 'sub_rust', name: 'Best Rust Courses' },
    { id: 'sub_react', name: 'Best React Component Libraries' },
    { id: 'sub_go', name: 'Best Go Web Frameworks' },
    { id: 'sub_sysdesign', name: 'Best System Design Resources' }
  ],
  cat_design: [
    { id: 'sub_figma', name: 'Best Figma Plugins for UI Designers' },
    { id: 'sub_color', name: 'Best Palette Generators for Dark Mode' }
  ],
  cat_datascience: [
    { id: 'sub_llm', name: 'Best Local LLM Interfaces (Ollama/LM Studio)' },
    { id: 'sub_python_data', name: 'Best Python Data Visualization Libraries' }
  ],
  cat_devops: [
    { id: 'sub_docker', name: 'Best Lightweight Container Runtimes' }
  ],
  cat_gamedev: []
};

export const namingConventionPattern = /^Best\s+[\w\s\-+]+(\s+for\s+[\w\s\-+]+)?$/i;
