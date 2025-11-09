# STEM Question Bank Management System

A modern, responsive web application for managing STEM competition questions with a comprehensive admin interface.

## Features

### 🎯 Core Functionality
- **Question Management**: Add, edit, delete, and review questions
- **Subject & Topic Organization**: Hierarchical organization of questions by subject and topic
- **Question Types**: Support for MCQ, True/False, Short Answer, and Long Answer questions
- **Review & Approval System**: Workflow for approving, rejecting, or requesting revisions
- **Export Functionality**: Export questions in PDF, Excel, CSV, and JSON formats

### 📊 Dashboard
- Summary cards showing total, approved, pending, and rejected questions
- Interactive pie chart displaying questions by subject
- Recent activity feed
- Quick action buttons

### 🔍 Advanced Features
- **Filtering & Search**: Filter questions by subject, topic, difficulty, status, and more
- **Pagination**: Efficient handling of large question sets
- **Responsive Design**: Works seamlessly on desktop and tablet devices
- **Modern UI**: Clean, academic design using TailwindCSS

## Technology Stack

### Frontend
- **Vue 3** with Composition API
- **TypeScript** for type safety
- **TailwindCSS** for styling
- **PrimeVue** for UI components
- **Chart.js** for data visualization
- **Inertia.js** for seamless SPA experience

### Backend
- **Laravel 11** PHP framework
- **SQLite** database (easily configurable for MySQL/PostgreSQL)
- **Inertia** for API-less development

## Installation

### Prerequisites
- PHP 8.2+
- Node.js 18+
- Composer
- npm or yarn

### Setup Steps

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd stem-question-management
   ```

2. **Install PHP dependencies**
   ```bash
   composer install
   ```

3. **Install Node.js dependencies**
   ```bash
   npm install
   ```

4. **Environment setup**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

5. **Database setup**
   ```bash
   php artisan migrate
   php artisan db:seed
   ```

6. **Build frontend assets**
   ```bash
   npm run build
   ```

7. **Start development server**
   ```bash
   php artisan serve
   npm run dev
   ```

## Project Structure

```
resources/js/
├── components/          # Reusable Vue components
│   ├── QuestionForm.vue    # Question creation/editing form
│   └── ReviewQuestion.vue  # Question review modal
├── layouts/            # Page layouts
│   └── app/
│       └── AppSidebarLayout.vue  # Main application layout
├── pages/              # Page components
│   ├── Dashboard.vue       # Dashboard with statistics
│   ├── Questions.vue       # Questions management
│   ├── Subjects.vue        # Subjects management
│   ├── Topics.vue          # Topics management
│   └── Export.vue          # Export functionality
└── app.ts              # Main application entry point
```

## Usage

### Adding Questions
1. Navigate to the Questions page
2. Click "Add Question" button
3. Fill in the required fields:
   - Subject and Topic
   - Difficulty level
   - Question type
   - Question text
   - Options (for MCQ questions)
   - Explanation and tags (optional)

### Managing Subjects & Topics
- **Subjects**: Create and manage main subject categories
- **Topics**: Organize questions within subjects with descriptions and difficulty levels

### Review Process
1. Questions start with "pending" status
2. Reviewers can:
   - **Approve**: Question becomes available for use
   - **Reject**: Question is marked as rejected
   - **Request Revision**: Send back with specific requirements

### Exporting Questions
1. Go to the Export page
2. Apply filters to select specific questions
3. Choose export format (PDF, Excel, CSV, JSON)
4. Configure export settings
5. Preview and download

## API Endpoints

The system uses Inertia.js for seamless frontend-backend communication. Key routes include:

- `GET /dashboard` - Dashboard page
- `GET /questions` - Questions management
- `GET /subjects` - Subjects management
- `GET /topics` - Topics management
- `GET /exports` - Export functionality

## Customization

### Adding New Question Types
1. Update the `QuestionForm.vue` component
2. Add validation logic
3. Update the question interface in TypeScript
4. Modify the display logic in relevant components

### Styling
- Modify TailwindCSS classes in components
- Update the color scheme in `tailwind.config.js`
- Customize PrimeVue theme

### Database Schema
- Add new fields to the questions table
- Update the Question model
- Modify the frontend interfaces accordingly

## Development

### Running Tests
```bash
php artisan test
```

### Code Quality
```bash
npm run lint
npm run format
```

### Building for Production
```bash
npm run build
```

## Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Add tests if applicable
5. Submit a pull request

## License

This project is licensed under the MIT License.

## Support

For support and questions, please open an issue in the repository or contact the development team.

---

**Built with ❤️ for STEM education**
# stem-question-bank
